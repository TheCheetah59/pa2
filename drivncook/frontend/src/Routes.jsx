import { Routes, Route } from "react-router-dom";
import Accueil from "./pages/Accueil";
import Login from "./pages/Login";
import Register from "./pages/Register";
import Profile from "./pages/Profile";
import Admin from "./pages/Admin";
import ProtectedRoute from "./components/ProtectedRoute";
import NotFound from "./pages/NotFound";
import Menu from "./pages/Menu";
import Contact from "./pages/Contact";
import ActivationWaiting from "./pages/ActivationWaiting";
import ActivationCallback from "./pages/ActivationCallback";


const AppRoutes = () => (
  <Routes>
    <Route path="/" element={<Accueil />} />
    <Route path="/login" element={<Login />} />
    <Route path="/register" element={<Register />} />
    <Route path="/waiting" element={<ActivationWaiting />} />
    <Route path="/activate/:token" element={<ActivationCallback />} />
    <Route element={<ProtectedRoute />}>
      <Route path="/menu" element={<Menu />} />
      <Route path="/contact" element={<Contact />} />
      <Route path="/profile" element={<Profile />} />
    </Route>
    <Route element={<ProtectedRoute roles={["admin"]} />}>
      <Route path="/admin" element={<Admin />} />
    </Route>
    <Route path="*" element={<NotFound />} />
  </Routes>
);

export default AppRoutes;
