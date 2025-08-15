import { Navigate } from "react-router-dom";
import { useAuth } from "../useAuth";

const ProtectedRoute = ({ children }) => {
  const { user } = useAuth();
  if (!user) {
    return <Navigate to="/login" replace />;
  }
  if (!user.is_activated) {
    return <Navigate to="/waiting" replace />;
  }
  return children;
};

export default ProtectedRoute;
