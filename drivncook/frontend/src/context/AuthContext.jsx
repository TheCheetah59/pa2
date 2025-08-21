// src/context/AuthContext.jsx
import { createContext, useContext, useState, useEffect, useMemo } from "react";
import api from "../axios";  

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

  // Restauration de session
  useEffect(() => {
    let alive = true;

    if (["/login", "/register"].includes(window.location.pathname)) {
      setLoading(false);
      return () => {
        alive = false;
      };
    }

    api
      .get("/api/auth/me") // ✅ API (pas /api/me ni /me)
      .then(({ data }) => {
        if (!alive) return;
        setUser(data);
        localStorage.setItem("user", JSON.stringify(data));
      })
      .catch((err) => {
        if (err?.response?.status !== 401) {
          console.error("Init /api/auth/me échoué:", err);
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

  // --- Connexion (admin/franchise côté API "auth", client côté API "customer") ---
  const login = async (credentials, isCustomer = false) => {
    // 1) cookie CSRF (hors /api)
    await api.get("/sanctum/csrf-cookie");

    // 2) endpoint API correct
    const url = isCustomer ? "/api/customer/login" : "/api/auth/login";

    try {
      const { data } = await api.post(url, credentials, {
        headers: { Accept: "application/json" },
        withCredentials: true,
      });

      const current = data.user ?? data.customer ?? data;
      setUser(current);
      localStorage.setItem("user", JSON.stringify(current));
      return current;
    } catch (error) {
      throw new Error(error?.response?.data?.message || "Échec de connexion");
    }
  };

  // --- Déconnexion ---
  const logout = async () => {
    try {
      // client -> /api/customer/logout, sinon -> /api/auth/logout
      const isClient = user?.role === "client" || user?.role === "customer";
      const url = isClient ? "/api/customer/logout" : "/api/auth/logout";
      await api.post(url, {}, { withCredentials: true });
    } finally {
      setUser(null);
      localStorage.removeItem("user");
    }
  };

  // --- Inscription (Mission 1 : rôle "franchise" par défaut côté backend) ---
  const register = async (payload) => {
    await api.get("/sanctum/csrf-cookie"); // ✅ hors /api

    try {
      const { data } = await api.post("/api/auth/register", payload, {
        headers: { Accept: "application/json" },
        withCredentials: true,
      });

      // Si ton backend exige l'activation par email, NE PAS connecter ici :
      // return data;
      // Sinon, si l'API renvoie l'utilisateur et que tu veux auto-login :
      const current = data.user ?? data.customer ?? data;
      if (current) {
        setUser(current);
        localStorage.setItem("user", JSON.stringify(current));
      }
      return current ?? data;
    } catch (error) {
      // Laisse le composant gérer les 422 (errors: {...})
      throw error;
    }
  };

  const value = useMemo(
    () => ({ user, loading, login, logout, register }),
    [user, loading]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

export const useAuth = () => useContext(AuthContext);
