// src/pages/Register.jsx
import { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";
import "./styles/Auth.css";

export default function Register() {
  const { register } = useAuth();
  const navigate = useNavigate();

  const [form, setForm] = useState({
    name: "",
    email: "",
    role: "franchise", // OK si ton backend l'accepte, sinon supprime ce champ
    password: "",
    password_confirmation: "",
  });

  const [errors, setErrors] = useState({});
  const [message, setMessage] = useState(null); // { type: 'success'|'error', text: string }
  const [submitting, setSubmitting] = useState(false);

  const handleChange = (e) =>
    setForm((f) => ({ ...f, [e.target.name]: e.target.value }));

  const submit = async (e) => {
    e.preventDefault();
    setErrors({});
    setMessage(null);
    setSubmitting(true);

    try {
      const { message } = await register(form); // ne connecte PAS l’utilisateur
      // 1) Affiche le message succès
      setMessage({
        type: "success",
        text:
          message ||
          "Compte créé ! Vérifie tes emails pour activer ton compte.",
      });
      // 2) Redirige vers la page d’attente avec l’email prérempli
      setTimeout(
        () =>
          navigate("/activation/waiting", {
            state: { email: form.email },
            replace: true,
          }),
        800
      );
    } catch (err) {
      if (err?.response?.status === 422) {
        setErrors(err.response.data?.errors || {});
      } else if (err?.response?.status === 429) {
        setMessage({
          type: "error",
          text: "Trop de tentatives. Réessaie dans quelques instants.",
        });
      } else if (err?.code === "ERR_NETWORK") {
        setMessage({
          type: "error",
          text: "Serveur injoignable. Vérifie l’URL API/connexion.",
        });
      } else {
        const msg =
          err?.response?.data?.message ||
          err?.message ||
          "Une erreur est survenue. Merci de réessayer.";
        setMessage({ type: "error", text: msg });
      }
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <form onSubmit={submit} className="auth-form" noValidate>
      {message && (
        <div
          className={`auth-message ${
            message.type === "error" ? "auth-error" : "auth-success"
          }`}
          role="status"
          aria-live="polite"
          style={{ marginBottom: 12 }}
        >
          {message.text}
        </div>
      )}

      <div className="auth-field">
        <label htmlFor="register-name">Nom</label>
        <input
          id="register-name"
          className={`auth-input ${errors.name ? "auth-input-error" : ""}`}
          type="text"
          name="name"
          value={form.name}
          onChange={handleChange}
          required
          autoComplete="name"
          aria-describedby={errors.name ? "register-name-error" : undefined}
          disabled={submitting}
        />
        {errors.name && (
          <small id="register-name-error" className="auth-message auth-error">
            {errors.name[0]}
          </small>
        )}
      </div>

      <div className="auth-field">
        <label htmlFor="register-email">Email</label>
        <input
          id="register-email"
          className={`auth-input ${errors.email ? "auth-input-error" : ""}`}
          type="email"
          name="email"
          value={form.email}
          onChange={handleChange}
          required
          autoComplete="email"
          aria-describedby={errors.email ? "register-email-error" : undefined}
          disabled={submitting}
        />
        {errors.email && (
          <small id="register-email-error" className="auth-message auth-error">
            {errors.email[0]}
          </small>
        )}
      </div>

      <div className="auth-field">
        <label htmlFor="register-role">Rôle</label>
        <select
          id="register-role"
          className={`auth-input ${errors.role ? "auth-input-error" : ""}`}
          name="role"
          value={form.role}
          onChange={handleChange}
          aria-describedby={errors.role ? "register-role-error" : undefined}
          disabled={submitting}
        >
          <option value="client">client</option>
          <option value="franchise">franchise</option>
        </select>
        {errors.role && (
          <small id="register-role-error" className="auth-message auth-error">
            {errors.role[0]}
          </small>
        )}
      </div>

      <div className="auth-field">
        <label htmlFor="register-password">Mot de passe</label>
        <input
          id="register-password"
          className={`auth-input ${errors.password ? "auth-input-error" : ""}`}
          type="password"
          name="password"
          value={form.password}
          onChange={handleChange}
          required
          autoComplete="new-password"
          aria-describedby={
            errors.password ? "register-password-error" : undefined
          }
          disabled={submitting}
        />
        {errors.password && (
          <small
            id="register-password-error"
            className="auth-message auth-error"
          >
            {errors.password[0]}
          </small>
        )}
      </div>

      <div className="auth-field">
        <label htmlFor="register-password-confirmation">
          Confirmez le mot de passe
        </label>
        <input
          id="register-password-confirmation"
          className={`auth-input ${
            errors.password_confirmation ? "auth-input-error" : ""
          }`}
          type="password"
          name="password_confirmation"
          value={form.password_confirmation}
          onChange={handleChange}
          required
          autoComplete="new-password"
          aria-describedby={
            errors.password_confirmation
              ? "register-password-confirmation-error"
              : undefined
          }
          disabled={submitting}
        />
        {errors.password_confirmation && (
          <small
            id="register-password-confirmation-error"
            className="auth-message auth-error"
          >
            {errors.password_confirmation[0]}
          </small>
        )}
      </div>

      <button type="submit" className="auth-btn" disabled={submitting}>
        {submitting ? "Création..." : "S'inscrire"}
      </button>

      <p className="auth-message">
        <Link to="/login">Déjà inscrit ?</Link>
      </p>
    </form>
  );
}
