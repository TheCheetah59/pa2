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
    role: "franchise", // ✅ valeur par défaut pour éviter l'input "non contrôlé"
    password: "",
    password_confirmation: "",
  });

  const [errors, setErrors] = useState({});
  const [message, setMessage] = useState(null); // { type: 'success'|'error', text: string }
  const [submitting, setSubmitting] = useState(false);

  const handleChange = (e) =>
    setForm({ ...form, [e.target.name]: e.target.value });

  const submit = async (e) => {
    e.preventDefault();
    setErrors({});
    setMessage(null);
    setSubmitting(true);

    try {
      await register(form); // doit throw si 4xx/5xx
      setMessage({
        type: "success",
        text: "Compte créé ! Vérifiez vos emails pour activer votre compte.",
      });
      // Si l'activation par email est en place, on redirige plutôt vers /login
      setTimeout(() => navigate("/login", { replace: true }), 1200);
    } catch (err) {
      if (err?.response?.status === 422) {
        setErrors(err.response.data.errors || {});
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
          className="auth-input"
          type="text"
          name="name"
          value={form.name}
          onChange={handleChange}
          required
          autoComplete="name"
          aria-describedby="register-name-error"
        />
        {errors.name && (
          <div aria-live="polite">
            <small id="register-name-error" className="auth-message auth-error">
              {errors.name[0]}
            </small>
          </div>
        )}
      </div>

      <div className="auth-field">
        <label htmlFor="register-email">Email</label>
        <input
          id="register-email"
          className="auth-input"
          type="email"
          name="email"
          value={form.email}
          onChange={handleChange}
          required
          autoComplete="email"
          aria-describedby="register-email-error"
        />
        {errors.email && (
          <div aria-live="polite">
            <small
              id="register-email-error"
              className="auth-message auth-error"
            >
              {errors.email[0]}
            </small>
          </div>
        )}
      </div>

      <div className="auth-field">
        <label htmlFor="register-role">Rôle</label>
        <select
          id="register-role"
          className="auth-input"
          name="role"
          value={form.role}
          onChange={handleChange}
          aria-describedby="register-role-error"
        >
          <option value="client">client</option>
          <option value="franchise">franchise</option>
        </select>
        {errors.role && (
          <div aria-live="polite">
            <small id="register-role-error" className="auth-message auth-error">
              {errors.role[0]}
            </small>
          </div>
        )}
      </div>

      <div className="auth-field">
        <label htmlFor="register-password">Mot de passe</label>
        <input
          id="register-password"
          className="auth-input"
          type="password"
          name="password"
          value={form.password}
          onChange={handleChange}
          required
          autoComplete="new-password"
          aria-describedby="register-password-error"
        />
        {errors.password && (
          <div aria-live="polite">
            <small
              id="register-password-error"
              className="auth-message auth-error"
            >
              {errors.password[0]}
            </small>
          </div>
        )}
      </div>

      <div className="auth-field">
        <label htmlFor="register-password-confirmation">
          Confirmez le mot de passe
        </label>
        <input
          id="register-password-confirmation"
          className="auth-input"
          type="password"
          name="password_confirmation"
          value={form.password_confirmation}
          onChange={handleChange}
          required
          autoComplete="new-password"
          aria-describedby="register-password-confirmation-error"
        />
        {errors.password_confirmation && (
          <div aria-live="polite">
            <small
              id="register-password-confirmation-error"
              className="auth-message auth-error"
            >
              {errors.password_confirmation[0]}
            </small>
          </div>
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
