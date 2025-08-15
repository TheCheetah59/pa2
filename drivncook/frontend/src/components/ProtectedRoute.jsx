import { Navigate, Outlet } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";

const roleRedirect = {
  client: "/dashboard",
  franchise: "/dashboard-franchise",
  admin: "/dashboard-admin",
};

const ProtectedRoute = ({ roles }) => {
  const { user } = useAuth();

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  if (!user.is_activated) {
    return <Navigate to="/waiting" replace />;
  }

  if (roles && !roles.includes(user.role)) {
    return <Navigate to={roleRedirect[user.role] || "/dashboard"} replace />;
  }

  return <Outlet />;
};

export default ProtectedRoute;
