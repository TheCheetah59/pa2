import { createContext, useContext, useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import api from "./axios";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const navigate = useNavigate();
  const [user, setUser] = useState(() => {
    const stored = localStorage.getItem("user");
    return stored ? JSON.parse(stored) : null;
  });

  useEffect(() => {
    const fetchUser = async () => {
      try {
        const { data } = await api.get("/user");
        setUser(data);
        localStorage.setItem("user", JSON.stringify(data));
        if (!data.is_activated) {
          navigate("/waiting");
        }
      } catch (err) {
        if (err.response?.status === 401) {
          setUser(null);
          localStorage.removeItem("user");
          navigate("/login");
        } else if (err.response?.status === 403) {
          navigate("/waiting");
        }
      }
    };

    fetchUser();

    const interceptor = api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          setUser(null);
          localStorage.removeItem("user");
          navigate("/login");
        } else if (error.response?.status === 403) {
          navigate("/waiting");
        }
        return Promise.reject(error);
      }
    );

    return () => {
      api.interceptors.response.eject(interceptor);
    };
  }, [navigate]);

  const login = async (payload) => {
    await api.get("/sanctum/csrf-cookie");
    const { data } = await api.post("/login", payload);
    setUser(data.user); // backend should return authenticated user
    localStorage.setItem("user", JSON.stringify(data.user));
    return data;
  };

  const register = async (payload) => {
    await api.get("/sanctum/csrf-cookie");
    const { data } = await api.post("/register", payload);
    setUser(data.user);
    localStorage.setItem("user", JSON.stringify(data.user));
    return data;
  };

  const logout = async () => {
    await api.post("/logout");
    setUser(null);
    localStorage.removeItem("user");
  };

  const isAuthenticated = !!user;

  return (
    <AuthContext.Provider value={{ user, login, register, logout, isAuthenticated }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);