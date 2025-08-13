import { createContext, useContext, useState } from "react";
import api from "./axios";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);

  const login = async (payload) => {
    await api.get("/sanctum/csrf-cookie");
    const { data } = await api.post("/login", payload);
    setUser(data.user); // backend should return authenticated user
    return data;
  };

  const register = async (payload) => {
    await api.get("/sanctum/csrf-cookie");
    const { data } = await api.post("/register", payload);
    setUser(data.user);
    return data;
  };

  const logout = async () => {
    await api.post("/logout");
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext); // eslint-disable-line react-refresh/only-export-components
