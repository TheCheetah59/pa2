// src/Routes.jsx
import { Routes, Route, Navigate } from "react-router-dom";
import Accueil from "./pages/Accueil";
import Login from "./pages/Login";
import Register from "./pages/Register";
import Profile from "./pages/Profile";
import Admin from "./pages/Admin";
import ProtectedRoute from "./components/ProtectedRoute";
import NotFound from "./pages/NotFound";
import Menu from "./pages/Menu";
import Checkout from "./pages/Checkout";
import Contact from "./pages/Contact";
import ActivationWaiting from "./pages/ActivationWaiting";
import ActivationCallback from "./pages/ActivationCallback";
import ActivationVerify from "./pages/ActivationVerify";

const AppRoutes = () => (
  <Routes>
    <Route path="/" element={<Accueil />} />
    <Route path="/login" element={<Login />} />
    <Route path="/register" element={<Register />} />

    {/* Nouvelles routes d’activation */}
    <Route path="/activation/waiting" element={<ActivationWaiting />} />
    <Route path="/activation/callback" element={<ActivationCallback />} />
    <Route path="/email/verify/:id/:hash" element={<ActivationVerify />} />

    {/* Compat : redirige l’ancienne mécanique */}
    <Route
      path="/waiting"
      element={<Navigate to="/activation/waiting" replace />}
    />
    <Route
      path="/activate/:token"
      element={<Navigate to="/activation/waiting" replace />}
    />

    <Route element={<ProtectedRoute />}>
      <Route path="/menu" element={<Menu />} />
      <Route path="/checkout/:orderId" element={<Checkout />} />
      <Route path="/profile" element={<Profile />} />
    </Route>

    <Route element={<ProtectedRoute roles={["admin"]} />}>
      <Route path="/admin" element={<Admin />} />
    </Route>

    <Route path="/contact" element={<Contact />} />
    <Route path="*" element={<NotFound />} />
  </Routes>
);

export default AppRoutes;
