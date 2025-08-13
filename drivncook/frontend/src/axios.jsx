import axios from "axios";

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || "http://localhost:8000", // Laravel URL
  withCredentials: true, // let Sanctum’s cookie travel
  headers: { "X-Requested-With": "XMLHttpRequest" },
});

export default api;
