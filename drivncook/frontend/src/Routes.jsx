import { Routes, Route } from "react-router-dom";
import Accueil from "./pages/Accueil";
import Login from "./pages/Login";
import Register from "./pages/Register";
import Menu from "./pages/Menu"; // ou Dashboard
import ProtectedRoute from "./components/ProtectedRoute";
import NotFound from "./pages/NotFound";
import Waiting from "./pages/Waiting";

const AppRoutes = () => (
  <Routes>
    <Route path="/" element={<Accueil />} />
    <Route path="/login" element={<Login />} />
    <Route path="/register" element={<Register />} />
    <Route path="/waiting" element={<Waiting />} />
    <Route
      path="/menu"
      element={
        <ProtectedRoute>
          <Menu />
        </ProtectedRoute>
      }
    />
    <Route path="*" element={<NotFound />} />
  </Routes>
);

export default AppRoutes;
