// src/axios.js
import axios from "axios";

const api = axios.create({
  baseURL: import.meta?.env?.VITE_API_URL ?? "http://localhost:8000",
  withCredentials: true,
  headers: { "X-Requested-With": "XMLHttpRequest" },
  xsrfCookieName: "XSRF-TOKEN", // ← Cookies/Headers attendus par Laravel
  xsrfHeaderName: "X-XSRF-TOKEN",
});

// Interceptor de réponse : gère 419 (CSRF) et 401
api.interceptors.response.use(
  (res) => res,
  async (error) => {
    const { config, response } = error || {};
    const status = response?.status;

    // 419 = CSRF token mismatch -> reprendre un cookie et rejouer UNE fois
    if (status === 419 && !config?._retry) {
      try {
        await api.get("/sanctum/csrf-cookie");
        config._retry = true;
        return api(config);
      } catch (e) {
        // si ça échoue, on laisse tomber
      }
    }

    // 401 = non authentifié
    // Si c'est l'appel d'init sur /api/me au démarrage, on laisse passer sans bruit
    if (
      status === 401 &&
      typeof config?.url === "string" &&
      config.url.includes("/api/me")
    ) {
      return Promise.reject(error); // ton AuthContext gère déjà ce cas
    }

    return Promise.reject(error);
  }
);

export default api;
