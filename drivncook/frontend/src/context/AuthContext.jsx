// src/context/AuthContext.jsx
import {
  createContext,
  useContext,
  useState,
  useEffect,
  useMemo,
  useCallback,
} from "react";
import api from "../axios"; // axios.create({ baseURL: VITE_API_URL, withCredentials: true })

/**
 * Contrat des endpoints côté backend :
 * - GET    /sanctum/csrf-cookie
 * - POST   /api/auth/login            -> 204 (No Content)
 * - POST   /api/auth/logout           -> 204
 * - POST   /api/auth/register         -> 201 { message, user? }
 * - GET    /api/auth/me               -> 200 { id, name, email, role }
 * - POST   /api/auth/email/resend     -> 200 { message }
 *
 * NB : login/logout doivent être sous middleware "web" côté Laravel (sessions/CSRF).
 */

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
  // --- state -----------------------------------------------------------------
  const [user, setUser] = useState(() => {
    try {
      const raw = localStorage.getItem("user");
      return raw ? JSON.parse(raw) : null;
    } catch {
      return null;
    }
  });
  const [loading, setLoading] = useState(true);

  // --- helpers ----------------------------------------------------------------
  const saveUser = useCallback((u) => {
    setUser(u);
    try {
      if (u) localStorage.setItem("user", JSON.stringify(u));
      else localStorage.removeItem("user");
    } catch {
      // ignore quota/JSON issues
    }
  }, []);

  const me = useCallback(async () => {
    // Récupère l'utilisateur courant (protégé par auth:sanctum)
    const { data } = await api.get("/api/auth/me", {
      headers: { Accept: "application/json" },
      // Évite la mise en cache agressive de certains proxys
      params: { _t: Date.now() },
    });
    saveUser(data);
    return data;
  }, [saveUser]);

  // --- restauration de session au démarrage ----------------------------------
  useEffect(() => {
    let alive = true;

    const path = window.location.pathname;
    // On évite l'appel /me sur les pages login & register pour accélérer l'affichage
    if (path === "/login" || path === "/register") {
      setLoading(false);
      return () => {
        alive = false;
      };
    }

    (async () => {
      try {
        // Si la session existe côté serveur, /me renverra 200 avec l'utilisateur
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
  }, [me, saveUser]);

  // --- Auth API ---------------------------------------------------------------

  /**
   * Login (Sanctum, session cookie)
   * @param {{ email: string, password: string }} credentials
   * @param {boolean} isCustomer Utilise /api/customer/login si vrai (si tu as 2 endpoints)
   * @returns {Promise<object>} Utilisateur courant (issu de /api/auth/me)
   */
  const login = useCallback(
    async (credentials, isCustomer = false) => {
      // Étape 1 : préparer le cookie XSRF (selon config Sanctum)
      try {
        await api.get("/sanctum/csrf-cookie");
      } catch {
        // Selon la config, l'appel peut déjà être fait par un autre écran — on ignore
      }

      // Étape 2 : POST login (retour attendu 204 No Content)
      const url = isCustomer ? "/api/customer/login" : "/api/auth/login";
      try {
        await api.post(url, credentials, {
          headers: { Accept: "application/json" },
        });

        // Étape 3 : récupérer l'utilisateur courant
        const current = await me();
        return current;
      } catch (err) {
        // Ne pas wrapper l'erreur pour conserver err.response.status et le message backend
        throw err;
      }
    },
    [me]
  );

  /**
   * Logout (toujours tenter côté serveur, puis on nettoie le cache local)
   */
  const logout = useCallback(async () => {
    try {
      const isClient = user?.role === "client" || user?.role === "customer";
      const url = isClient ? "/api/customer/logout" : "/api/auth/logout";
      await api.post(url, {}, { headers: { Accept: "application/json" } });
    } finally {
      // On purge quoi qu'il arrive pour éviter les incohérences d'UI
      saveUser(null);
    }
  }, [user, saveUser]);

  /**
   * Register — ne connecte PAS l'utilisateur
   * @param {{ name?: string, email: string, password: string, password_confirmation: string, role?: string }} payload
   * @returns {Promise<{ message: string, user?: object }>}
   */
  const register = useCallback(async (payload) => {
    try {
      // Facultatif (si tu laisses register dans api.php, pas besoin de session)
      try {
        await api.get("/sanctum/csrf-cookie");
      } catch {}
      const { data } = await api.post("/api/auth/register", payload, {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      });

      return {
        message:
          data?.message ??
          "Inscription réussie. Vérifie ta boîte mail pour activer ton compte.",
        user: data?.user,
      };
    } catch (err) {
      throw err;
    }
  }, []);

  /**
   * Renvoyer l'email d'activation (ouvert, mais à rate-limiter côté backend)
   * @param {string} email
   * @returns {Promise<{ message: string }>}
   */
  const resendActivation = useCallback(async (email) => {
    try {
      const { data } = await api.post(
        "/api/auth/email/resend",
        { email },
        { headers: { Accept: "application/json" } }
      );
      return {
        message:
          data?.message ??
          "Si un compte existe et n'est pas vérifié, un email a été renvoyé.",
      };
    } catch (err) {
      throw err;
    }
  }, []);

  const value = useMemo(
    () => ({
      user,
      loading,
      login,
      logout,
      register,
      resendActivation,
      me,
      // utilitaire : true quand on a chargé l’état initial (utile pour ProtectedRoute)
      isReady: !loading,
      isAuthenticated: !!user,
    }),
    [user, loading, login, logout, register, resendActivation, me]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

export const useAuth = () => useContext(AuthContext);
