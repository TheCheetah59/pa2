import { createContext, useContext, useEffect, useState } from "react";
import api from "../axios";

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
    await api.get("/sanctum/csrf-cookie");
    const { data } = await api.post("/login", payload);
    setToken(data.token);
    setUser(data.user);
    localStorage.setItem("token", data.token);
    localStorage.setItem("user", JSON.stringify(data.user));
    api.defaults.headers.common.Authorization = `Bearer ${data.token}`;
    return data;
  };

  const register = async (payload) => {
    await api.get("/sanctum/csrf-cookie");
    const { data } = await api.post("/register", payload);
    setToken(data.token);
    setUser(data.user);
    localStorage.setItem("token", data.token);
    localStorage.setItem("user", JSON.stringify(data.user));
    api.defaults.headers.common.Authorization = `Bearer ${data.token}`;
    return data;
  };

  const logout = async () => {
    await api.post("/logout");
    setToken(null);
    setUser(null);
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    delete api.defaults.headers.common.Authorization;
  };

  return (
    <AuthContext.Provider value={{ user, token, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

// eslint-disable-next-line react-refresh/only-export-components
export const useAuth = () => useContext(AuthContext);
