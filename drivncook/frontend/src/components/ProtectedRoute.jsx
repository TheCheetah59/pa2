import { Navigate, Outlet } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";


const ProtectedRoute = ({ roles }) => {
  const { user, loading } = useAuth();

  if (loading) {
    return null;
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  if (
    Object.prototype.hasOwnProperty.call(user, "is_activated") &&
    !user.is_activated
  ) {
    return <Navigate to="/waiting" replace />;
  }

  if (roles && (!user.role || !roles.includes(user.role))) {
    return <Navigate to="/login" replace />;
  }

  return <Outlet />;
};

export default ProtectedRoute;
