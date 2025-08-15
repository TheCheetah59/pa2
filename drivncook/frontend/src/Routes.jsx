import { Routes, Route } from "react-router-dom";
import Accueil from "./pages/Accueil";
import Login from "./pages/Login";
import Register from "./pages/Register";
import ProtectedRoute from "./components/ProtectedRoute";
import NotFound from "./pages/NotFound";
import ActivationWaiting from "./pages/ActivationWaiting";
import DashboardClient from "./pages/DashboardClient";
import DashboardFranchise from "./pages/DashboardFranchise";
import DashboardAdmin from "./pages/DashboardAdmin";
import ActivationCallback from "./pages/ActivationCallback";

const AppRoutes = () => (
  <Routes>
    <Route path="/" element={<Accueil />} />
    <Route path="/login" element={<Login />} />
    <Route path="/register" element={<Register />} />
    <Route path="/waiting" element={<ActivationWaiting />} />
    <Route path="/activate/:token" element={<ActivationCallback />} />
    <Route
      path="/dashboard"
      element={
        <ProtectedRoute>
          <DashboardClient />
        </ProtectedRoute>
      }
    />
    <Route
      path="/dashboard-franchise"
      element={
        <ProtectedRoute>
          <DashboardFranchise />
        </ProtectedRoute>
      }
    />
    <Route
      path="/dashboard-admin"
      element={
        <ProtectedRoute>
          <DashboardAdmin />
        </ProtectedRoute>
      }
    />
    <Route path="*" element={<NotFound />} />
  </Routes>
);

export default AppRoutes;
