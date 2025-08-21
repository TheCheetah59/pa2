// src/context/AuthContext.jsx
import { createContext, useContext, useState, useEffect, useMemo } from "react";
import api from "../axios"; // axios.create({ baseURL: VITE_API_URL, withCredentials: true })

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  // Cache local facultatif (utile pour éviter un flash au reload)
  const [user, setUser] = useState(() => {
    try {
      return JSON.parse(localStorage.getItem("user") || "null");
    } catch {
      return null;
    }
  });
  const [loading, setLoading] = useState(true);

  // ---- helpers --------------------------------------------------------------
  const saveUser = (u) => {
    setUser(u);
    if (u) localStorage.setItem("user", JSON.stringify(u));
    else localStorage.removeItem("user");
  };

  const me = async () => {
    const { data } = await api.get("/api/auth/me");
    saveUser(data);
    return data;
  };

  // ---- restauration de session au démarrage --------------------------------
  useEffect(() => {
    let alive = true;

    const path = window.location.pathname;
    // On évite l'appel initial /me sur les pages /login & /register
    if (path === "/login" || path === "/register") {
      setLoading(false);
      return () => {
        alive = false;
      };
    }

    (async () => {
      try {
        const data = await me();
        if (!alive) return;
        saveUser(data);
      } catch (err) {
        // 401 attendu si pas connecté
        if (alive) saveUser(null);
      } finally {
        if (alive) setLoading(false);
      }
    })();

    return () => {
      alive = false;
    };
  }, []);

  // ---- Auth -----------------------------------------------------------------

  /**
   * Login (Sanctum cookie)
   * @param {{email:string,password:string}} credentials
   * @param {boolean} isCustomer si tu as vraiment 2 endpoints (client/admin)
   * @returns {Promise<object>} user courant
   * NOTE: on RELAIE l'erreur axios telle quelle (ne pas wrapper) -> status utilisable dans Login.jsx
   */
  const login = async (credentials, isCustomer = false) => {
    // Si tu utilises le flow Sanctum SPA, le csrf-cookie peut être utile
    try {
      // facultatif selon ta config, mais safe :
      await api.get("/sanctum/csrf-cookie");
    } catch {
      // ignore
    }

    // Si tu n'as QU'UN seul endpoint, garde "/api/auth/login" pour tout le monde
    const url = isCustomer ? "/api/customer/login" : "/api/auth/login";

    try {
      await api.post(url, credentials, {
        headers: { Accept: "application/json" },
      });
      // après succès: récupère l'utilisateur courant via /me
      const current = await me();
      return current;
    } catch (err) {
      // très important: NE PAS créer un nouvel Error() → on perd status & payload
      throw err;
    }
  };

  /**
   * Logout
   */
  const logout = async () => {
    try {
      const isClient = user?.role === "client" || user?.role === "customer";
      const url = isClient ? "/api/customer/logout" : "/api/auth/logout";
      await api.post(url, {});
    } finally {
      saveUser(null);
    }
  };

  /**
   * Register — ne connecte PAS l'utilisateur
   * @param {{name?:string,email:string,password:string,password_confirmation:string}} payload
   * @returns {Promise<{message:string}>}
   */
  const register = async (payload) => {
    try {
      // (facultatif) CSRF si back attend une session
      try {
        await api.get("/sanctum/csrf-cookie");
      } catch {}

      const { data } = await api.post("/api/auth/register", payload, {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      });

      // ❗️Pas d’auto-login ici
      // On retourne simplement le message du backend
      return {
        message:
          data?.message ??
          "Inscription réussie. Vérifie ta boîte mail pour activer ton compte.",
      };
    } catch (err) {
      // Laisse le composant gérer err.response.status === 422 etc.
      throw err;
    }
  };

  /**
   * Renvoyer l'email d'activation (ouvert, rate‑limité)
   * @param {string} email
   * @returns {Promise<{message:string}>}
   */
  const resendActivation = async (email) => {
    try {
      const { data } = await api.post("/api/auth/email/resend", { email });
      return {
        message:
          data?.message ??
          "Si un compte existe et n'est pas vérifié, un email a été renvoyé.",
      };
    } catch (err) {
      throw err;
    }
  };

  const value = useMemo(
    () => ({
      user,
      loading,
      login,
      logout,
      register, // retourne seulement un message
      resendActivation, // helper pour /activation/waiting
      me,
    }),
    [user, loading]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

export const useAuth = () => useContext(AuthContext);
