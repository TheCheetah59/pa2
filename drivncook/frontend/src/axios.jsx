// src/axios.jsx
import axios from "axios";

// --- helpers
function normalize(url) {
  if (!url) return "http://localhost:8000";
  if (!/^https?:\/\//i.test(url)) url = "http://" + url.replace(/^:+/, "");
  return url.replace(/\/+$/, "");
}
function readCookie(name) {
  const m = document.cookie.match(new RegExp("(^|; )" + name + "=([^;]*)"));
  return m ? decodeURIComponent(m[2]) : null;
}

// --- instance
const API_URL = normalize(import.meta?.env?.VITE_API_URL);
const api = axios.create({
  baseURL: API_URL,
  withCredentials: true,
  headers: { "X-Requested-With": "XMLHttpRequest" },
  xsrfCookieName: "XSRF-TOKEN",
  xsrfHeaderName: "X-XSRF-TOKEN",
});

console.log("[axios] baseURL =", api.defaults.baseURL);

// --- request: injecte toujours X-XSRF-TOKEN si dispo
api.interceptors.request.use((config) => {
  const token = readCookie("XSRF-TOKEN"); // pas httpOnly
  if (token) config.headers["X-XSRF-TOKEN"] = token;
  return config;
});

// --- response: si 419, récupère cookie et rejoue une fois
api.interceptors.response.use(
  (res) => res,
  async (error) => {
    const { config, response } = error || {};
    const status = response?.status;

    if (status === 419 && config && !config._retry) {
      console.warn("[axios] 419 -> fetching /sanctum/csrf-cookie then retry");
      try {
        await api.get("/sanctum/csrf-cookie"); // ⚠️ sans /api
        config._retry = true;
        // réinjecte le token au cas où
        const token = readCookie("XSRF-TOKEN");
        if (token) config.headers["X-XSRF-TOKEN"] = token;
        return api(config);
      } catch (e) {
        console.error("[axios] csrf refresh failed", e);
      }
    }
    return Promise.reject(error);
  }
);

export default api;
