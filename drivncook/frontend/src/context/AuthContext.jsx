import { createContext, useContext, useEffect, useState } from "react";
import api from "../axios";
import { getLogoutEndpoint } from "./getLogoutEndpoint.js";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(() => {
    const stored = localStorage.getItem("user");
    return stored ? JSON.parse(stored) : null;
  });
  const [token, setToken] = useState(() => localStorage.getItem("token"));

  useEffect(() => {
    if (token) {
      api.defaults.headers.common.Authorization = `Bearer ${token}`;
      const fetchUser = async () => {
        try {
          const { data } = await api.get("/api/me");
          setUser(data);
          localStorage.setItem("user", JSON.stringify(data));
        } catch {
          setToken(null);
          setUser(null);
          localStorage.removeItem("token");
          localStorage.removeItem("user");
          delete api.defaults.headers.common.Authorization;
        }
      };
      fetchUser();
    } else {
      delete api.defaults.headers.common.Authorization;
    }
  }, [token]);

  const login = async (payload) => {
    const { data } = await api.post("/api/login", payload);
    setToken(data.token);
    setUser(data.user);
    localStorage.setItem("token", data.token);
    localStorage.setItem("user", JSON.stringify(data.user));
    api.defaults.headers.common.Authorization = `Bearer ${data.token}`;
    return data;
  };

  const register = async (payload) => {
    await api.get("/sanctum/csrf-cookie"); // ← OBLIGATOIRE
    const { data } = await api.post("/api/register", payload);
    return data;
  };

  const signIn = async (payload) => {
    const data = await login(payload);
    return {
      message: data.message || "Connexion réussie !",
      user: data.user,
    };
  };

  const signUp = async (payload) => {
    await register(payload);
    return { message: "Inscription réussie !" };
  };

  const logout = async () => {
    const endpoint = getLogoutEndpoint(user);
    await api.post(endpoint);
    setToken(null);
    setUser(null);
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    delete api.defaults.headers.common.Authorization;
  };

  return (
    <AuthContext.Provider
      value={{ user, token, login, register, logout, signIn, signUp }}
    >
      {children}
    </AuthContext.Provider>
  );
};

// eslint-disable-next-line react-refresh/only-export-components
export const useAuth = () => useContext(AuthContext);
