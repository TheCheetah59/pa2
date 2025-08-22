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
 *
 * SANCTUM (Admin/Franchise) :
 * - GET    /sanctum/csrf-cookie
 * - POST   /api/auth/login            -> 204 (No Content)
 * - POST   /api/auth/logout           -> 204
 * - POST   /api/auth/register         -> 201 { message, user? }
 * - GET    /api/auth/me               -> 200 { id, name, email, role }
 * - POST   /api/auth/email/resend     -> 200 { message }
 *
 * CUSTOMER (Guard customer) :
 * - POST   /api/customer/login        -> 204 (No Content)
 * - POST   /api/customer/logout       -> 204
 * - GET    /api/customer/profile      -> 200 { id, name, email, role }
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

  const [userType, setUserType] = useState(() => {
    try {
      return localStorage.getItem("userType") || "admin"; // "admin" ou "customer"
    } catch {
      return "admin";
    }
  });

  const [loading, setLoading] = useState(true);

  // --- helpers ----------------------------------------------------------------
  const saveUser = useCallback((u, type = "admin") => {
    setUser(u);
    setUserType(type);

    try {
      if (u) {
        localStorage.setItem("user", JSON.stringify(u));
        localStorage.setItem("userType", type);
      } else {
        localStorage.removeItem("user");
        localStorage.removeItem("userType");
      }
    } catch {
      // ignore quota/JSON issues
    }
  }, []);

  // --- API calls différenciées par type d'utilisateur -----------------------

  /**
   * Récupère l'utilisateur admin/franchise (protégé par auth:sanctum)
   */
  const getAdminUser = useCallback(async () => {
    const { data } = await api.get("/api/auth/me", {
      headers: { Accept: "application/json" },
      params: { _t: Date.now() },
    });
    return data;
  }, []);

  /**
   * Récupère l'utilisateur customer (protégé par auth:customer)
   */
  const getCustomerUser = useCallback(async () => {
    const { data } = await api.get("/api/customer/profile", {
      headers: { Accept: "application/json" },
      params: { _t: Date.now() },
    });
    return data;
  }, []);

  /**
   * Récupère l'utilisateur selon le type stocké
   */
  const me = useCallback(async () => {
    let data;

    if (userType === "customer") {
      data = await getCustomerUser();
    } else {
      data = await getAdminUser();
    }

    saveUser(data, userType);
    return data;
  }, [userType, getAdminUser, getCustomerUser, saveUser]);

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
        // Essayer de récupérer l'utilisateur selon le type stocké
        if (userType === "customer") {
          try {
            const data = await getCustomerUser();
            if (alive) saveUser(data, "customer");
          } catch (customerErr) {
            // Si échec customer, essayer admin
            try {
              const data = await getAdminUser();
              if (alive) saveUser(data, "admin");
            } catch (adminErr) {
              if (alive) saveUser(null);
            }
          }
        } else {
          try {
            const data = await getAdminUser();
            if (alive) saveUser(data, "admin");
          } catch (adminErr) {
            // Si échec admin, essayer customer
            try {
              const data = await getCustomerUser();
              if (alive) saveUser(data, "customer");
            } catch (customerErr) {
              if (alive) saveUser(null);
            }
          }
        }
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
  }, [userType, getAdminUser, getCustomerUser, saveUser]);

  // --- Auth API ---------------------------------------------------------------

  /**
   * Login unifié pour les deux types d'utilisateurs
   * @param {{ email: string, password: string }} credentials
   * @param {boolean} isCustomer Si vrai, utilise le guard customer
   * @returns {Promise<object>} Utilisateur courant
   */
  const login = useCallback(
    async (credentials, isCustomer = false) => {
      console.log("🔍 Login attempt:", {
        isCustomer,
        email: credentials.email,
      });

      // Étape 1 : préparer le cookie XSRF (selon config Sanctum)
      try {
        await api.get("/sanctum/csrf-cookie");
        console.log("✅ CSRF cookie obtained");
      } catch (csrfErr) {
        console.warn("⚠️ CSRF cookie failed:", csrfErr);
      }

      const loginUrl = isCustomer ? "/api/customer/login" : "/api/auth/login";
      const type = isCustomer ? "customer" : "admin";

      console.log("🚀 Calling login URL:", loginUrl);

      try {
        // Étape 2 : POST login (retour attendu 204 No Content)
        const response = await api.post(loginUrl, credentials, {
          headers: { Accept: "application/json" },
        });

        console.log("✅ Login successful:", response.status);

        // Étape 3 : récupérer l'utilisateur selon le type
        let current;
        if (isCustomer) {
          current = await getCustomerUser();
        } else {
          current = await getAdminUser();
        }

        console.log("✅ User retrieved:", current);

        // Sauvegarder avec le bon type
        saveUser(current, type);
        return current;
      } catch (err) {
        console.error(
          "❌ Login failed:",
          err.response?.status,
          err.response?.data
        );
        throw err;
      }
    },
    [getAdminUser, getCustomerUser, saveUser]
  );

  /**
   * Logout unifié
   */
  const logout = useCallback(async () => {
    try {
      const logoutUrl =
        userType === "customer" ? "/api/customer/logout" : "/api/auth/logout";

      console.log("🚪 Logout from:", logoutUrl);

      await api.post(
        logoutUrl,
        {},
        {
          headers: { Accept: "application/json" },
        }
      );

      console.log("✅ Logout successful");
    } catch (err) {
      console.warn("⚠️ Logout error (cleaning anyway):", err);
    } finally {
      // On purge quoi qu'il arrive pour éviter les incohérences d'UI
      saveUser(null);
    }
  }, [userType, saveUser]);

  /**
   * Register — ne connecte PAS l'utilisateur (uniquement pour admin/franchise)
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

  /**
   * Inscription client (création de compte customer)
   * @param {{ name: string, email: string, password: string, password_confirmation: string }} payload
   * @returns {Promise<{ message: string, user?: object }>}
   */
  const registerCustomer = useCallback(async (payload) => {
    try {
      try {
        await api.get("/sanctum/csrf-cookie");
      } catch {}

      const { data } = await api.post("/api/customers", payload, {
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      });

      return {
        message: data?.message ?? "Compte client créé avec succès.",
        user: data?.user,
      };
    } catch (err) {
      throw err;
    }
  }, []);

  // --- Utilitaires ------------------------------------------------------------

  /**
   * Vérifie si l'utilisateur actuel est un admin
   */
  const isAdmin = useMemo(() => {
    return userType === "admin" && user?.role === "admin";
  }, [userType, user]);

  /**
   * Vérifie si l'utilisateur actuel est un franchisé
   */
  const isFranchisee = useMemo(() => {
    return (
      userType === "admin" &&
      (user?.role === "franchisee" || user?.role === "franchise")
    );
  }, [userType, user]);

  /**
   * Vérifie si l'utilisateur actuel est un client
   */
  const isCustomer = useMemo(() => {
    return userType === "customer";
  }, [userType]);

  const value = useMemo(
    () => ({
      // État
      user,
      userType,
      loading,

      // Actions
      login,
      logout,
      register,
      registerCustomer,
      resendActivation,
      me,

      // Utilitaires
      isReady: !loading,
      isAuthenticated: !!user,
      isAdmin,
      isFranchisee,
      isCustomer,

      // Types de redirection selon le rôle
      getRedirectPath: () => {
        if (isAdmin) return "/admin";
        if (isFranchisee) return "/franchise";
        if (isCustomer) return "/menu";
        return "/";
      },
    }),
    [
      user,
      userType,
      loading,
      login,
      logout,
      register,
      registerCustomer,
      resendActivation,
      me,
      isAdmin,
      isFranchisee,
      isCustomer,
    ]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error("useAuth doit être utilisé dans un AuthProvider");
  }
  return context;
};
