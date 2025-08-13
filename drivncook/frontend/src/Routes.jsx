import { Routes, Route } from "react-router-dom";
import Accueil from "./pages/Accueil";
import Login from "./pages/Login";
import Register from "./pages/Register";
import Menu from "./pages/Menu"; // ou Dashboard
import ProtectedRoute from "./components/ProtectedRoute";

const AppRoutes = () => (
  <Routes>
    <Route path="/" element={<Accueil />} />
    <Route path="/login" element={<Login />} />
    <Route path="/register" element={<Register />} />
    <Route
      path="/menu"
      element={
        <ProtectedRoute>
          <Menu />
        </ProtectedRoute>
      }
    />
  </Routes>
);

export default AppRoutes;
