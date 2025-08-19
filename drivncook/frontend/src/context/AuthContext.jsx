// src/context/AuthContext.jsx
import { createContext, useContext, useState, useEffect, useMemo } from "react";
import api from "../axios"; // axios avec withCredentials: true

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(() => {
    try {
      return JSON.parse(localStorage.getItem("user") || "null");
    } catch {
      return null;
    }
  });
  const [loading, setLoading] = useState(true);

  // Tentative de restauration de session au montage
  useEffect(() => {
    let alive = true;

    api
      .get("/api/me")
      .then(({ data }) => {
        if (!alive) return;
        setUser(data);
        localStorage.setItem("user", JSON.stringify(data));
      })
      .catch((err) => {
        // 401 = pas connecté => normal au premier rendu, on ignore
        if (err?.response?.status !== 401) {
          console.error("Init /api/me échoué:", err);
        }
        if (alive) {
          setUser(null);
          localStorage.removeItem("user");
        }
      })
      .finally(() => alive && setLoading(false));

    return () => {
      alive = false;
    };
  }, []);

  // --- Connexion (Sanctum session/cookies) ---
  const login = async (credentials) => {
    await api.get("/sanctum/csrf-cookie");
    const { data } = await api.post("/login", credentials);
    const currentUser = data.user ?? data;

    if (!currentUser?.is_activated) {
      await api.post("/logout");
      throw new Error("Compte non activé");
    }

    setUser(currentUser);
    localStorage.setItem("user", JSON.stringify(currentUser));
    return currentUser;
  };

  // --- Déconnexion ---
  const logout = async () => {
    try {
      await api.post("/logout");
    } finally {
      setUser(null);
      localStorage.removeItem("user");
    }
  };

  // --- Inscription (pas d’auto-login si activation mail requise) ---
  const register = async (payload) => {
    await api.get("/sanctum/csrf-cookie");
    return api.post("/register", payload);
  };

  const value = useMemo(
    () => ({ user, loading, login, logout, register }),
    [user, loading]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

export const useAuth = () => useContext(AuthContext);
