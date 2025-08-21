// src/pages/Login.jsx
import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";
import "./styles/Auth.css";

const Login = () => {
  const { login } = useAuth(); // doit renvoyer l'user courant, ou throw l'erreur axios
  const navigate = useNavigate();

  const [form, setForm] = useState({ email: "", password: "" });
  const [mode, setMode] = useState("customer"); // "customer" | "admin"
  const [loading, setLoading] = useState(false);

  const [errors, setErrors] = useState({});
  const [generalError, setGeneralError] = useState("");

  const handleChange = (e) => {
    setForm((f) => ({ ...f, [e.target.name]: e.target.value }));
  };

  const submit = async (e) => {
    e.preventDefault();
    setErrors({});
    setGeneralError("");
    setLoading(true);

    try {
      // 2e param = true si login "customer" (guard client), false sinon (admin / back-office)
      const current = await login(form, mode === "customer");

      // Redirection selon le rôle
      if (current?.role === "admin") {
        navigate("/admin", { replace: true });
      } else {
        // Franchisé / client → adapte si tu préfères /profile
        navigate("/menu", { replace: true });
      }
    } catch (err) {
      const status = err?.response?.status;

      if (status === 422) {
        // Erreurs de validation Laravel
        setErrors(err.response.data?.errors || {});
      } else if (status === 401) {
        // Identifiants invalides
        setGeneralError("Email ou mot de passe incorrect.");
        // Optionnel : vider seulement le mot de passe
        setForm((f) => ({ ...f, password: "" }));
      } else if (status === 423) {
        // Email non vérifié → on envoie l’utilisateur vers la page de renvoi
        navigate("/activation/waiting", {
          state: { email: form.email },
          replace: true,
        });
        return;
      } else if (status === 429) {
        setGeneralError("Trop de tentatives. Réessaie dans quelques instants.");
      } else if (err.code === "ERR_NETWORK") {
        setGeneralError(
          "Impossible de contacter le serveur. Vérifie ta connexion ou l’URL API."
        );
      } else {
        setGeneralError(
          err?.response?.data?.message || "Une erreur est survenue."
        );
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={submit} className="auth-form" noValidate>
      <h1 className="auth-title">Connexion</h1>

      {/* Sélecteur de mode */}
      <div className="auth-field" role="group" aria-labelledby="mode-label">
        <span id="mode-label" className="auth-label">
          Se connecter en tant que
        </span>
        <div className="auth-radio-row">
          <label className="auth-radio">
            <input
              type="radio"
              name="mode"
              value="customer"
              checked={mode === "customer"}
              onChange={() => setMode("customer")}
            />
            Client / Franchisé
          </label>
          <label className="auth-radio">
            <input
              type="radio"
              name="mode"
              value="admin"
              checked={mode === "admin"}
              onChange={() => setMode("admin")}
            />
            Admin
          </label>
        </div>
      </div>

      {/* Email */}
      <div className="auth-field">
        <label htmlFor="login-email">Email</label>
        <input
          id="login-email"
          className={`auth-input ${errors.email ? "auth-input-error" : ""}`}
          type="email"
          name="email"
          value={form.email}
          onChange={handleChange}
          required
          autoComplete="email"
          aria-describedby={errors.email ? "login-email-error" : undefined}
          disabled={loading}
        />
        {errors.email && (
          <div aria-live="polite">
            <small id="login-email-error" className="auth-message auth-error">
              {errors.email[0]}
            </small>
          </div>
        )}
      </div>

      {/* Password */}
      <div className="auth-field">
        <label htmlFor="login-password">Mot de passe</label>
        <input
          id="login-password"
          className={`auth-input ${errors.password ? "auth-input-error" : ""}`}
          type="password"
          name="password"
          value={form.password}
          onChange={handleChange}
          required
          autoComplete="current-password"
          aria-describedby={
            errors.password ? "login-password-error" : undefined
          }
          disabled={loading}
        />
        {errors.password && (
          <div aria-live="polite">
            <small
              id="login-password-error"
              className="auth-message auth-error"
            >
              {errors.password[0]}
            </small>
          </div>
        )}
      </div>

      {/* Submit */}
      <button type="submit" className="auth-btn" disabled={loading}>
        {loading ? "Connexion..." : "Connexion"}
      </button>

      {/* Lien register */}
      <p className="auth-message">
        <Link to="/register">Pas encore inscrit ?</Link>
      </p>

      {/* Erreur générale */}
      {generalError && (
        <div aria-live="polite">
          <p className="auth-message auth-error">{generalError}</p>
        </div>
      )}
    </form>
  );
};

export default Login;
