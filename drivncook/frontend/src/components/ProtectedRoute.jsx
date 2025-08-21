// src/components/ProtectedRoute.jsx
import { Navigate, Outlet } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";

const ProtectedRoute = ({ roles = [] }) => {
  const { user, loading } = useAuth();

  if (loading) return null; // ou un spinner

  // Pas connecté → login
  if (!user) {
    return <Navigate to="/login" replace />;
  }

  // Vérif email (nouvelle mécanique Laravel)
  const isVerified = !!user.email_verified_at; // string timestamp → truthy
  if (!isVerified) {
    return <Navigate to="/activation/waiting" replace />;
  }

  // Contrôle des rôles si demandé
  if (roles.length > 0 && !roles.includes(user.role)) {
    // redirige où tu préfères ("/" ou page 403)
    return <Navigate to="/login" replace />;
  }

  return <Outlet />;
};

export default ProtectedRoute;
