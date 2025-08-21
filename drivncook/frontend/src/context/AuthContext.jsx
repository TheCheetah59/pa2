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

    if (["/login", "/register"].includes(window.location.pathname)) {
      setLoading(false);
      return () => {
        alive = false;
      };
    }

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

  // --- Connexion (admin ou client) ---
  const login = async (credentials, isCustomer = false) => {
    await api.get("/sanctum/csrf-cookie");
    let data;
    try {
      ({ data } = await api.post(
        isCustomer ? "/customer/login" : "/login",
        credentials
      ));
    } catch (error) {
      throw new Error(error.response?.data?.message || "Échec de connexion");
    }
    const current = data.user ?? data.customer ?? data;

    setUser(current);
    localStorage.setItem("user", JSON.stringify(current));
    return current;
  };

  // --- Déconnexion ---
  const logout = async () => {
    try {
      if (user?.role === "admin") {
        await api.post("/logout");
      } else {
        await api.post("/customer/logout");
      }
    } finally {
      setUser(null);
      localStorage.removeItem("user");
    }
  };

  // --- Inscription client (auto-login) ---
  const register = async (payload) => {
    await api.get("/sanctum/csrf-cookie");
    let data;
    try {
      ({ data } = await api.post("/register", payload));
    } catch (error) {
      throw new Error(error.response?.data?.message || "Échec d'inscription");
    }
    const current = data.customer ?? data;
    setUser(current);
    localStorage.setItem("user", JSON.stringify(current));
    return current;
  };

  const value = useMemo(
    () => ({ user, loading, login, logout, register }),
    [user, loading]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};
// eslint-disable-next-line react-refresh/only-export-components

export const useAuth = () => useContext(AuthContext);
