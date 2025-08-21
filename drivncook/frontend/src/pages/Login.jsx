import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";
import "./styles/Auth.css";

const Login = () => {
  const { login } = useAuth();
  const navigate = useNavigate();
  const [form, setForm] = useState({ email: "", password: "" });
  const [errors, setErrors] = useState({});
  const [generalError, setGeneralError] = useState("");
  const [mode, setMode] = useState("customer"); // "customer" ou "admin"

  const handleChange = (e) =>
    setForm({ ...form, [e.target.name]: e.target.value });

  const submit = async (e) => {
    e.preventDefault();
    setErrors({});
    setGeneralError("");
    try {
      const current = await login(form, mode === "customer");
      if (current.role === "admin") {
        navigate("/admin");
      } else {
      }
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {});
      } else {
      }
    }
  };

  return (
    <form onSubmit={submit} className="auth-form">
      <div className="auth-field">
        <label htmlFor="login-email">Email</label>
        <input
          id="login-email"
          className="auth-input"
          type="email"
          name="email"
          value={form.email}
          onChange={handleChange}
          required
          autoComplete="email"
          aria-describedby="login-email-error"
        />
        {errors.email && (
          <div aria-live="polite">
            <small id="login-email-error" className="auth-message auth-error">
              {errors.email[0]}
            </small>
          </div>
        )}
      </div>
      <div className="auth-field">
        <label htmlFor="login-password">Mot de passe</label>
        <input
          id="login-password"
          className="auth-input"
          type="password"
          name="password"
          value={form.password}
          onChange={handleChange}
          required
          autoComplete="current-password"
          aria-describedby="login-password-error"
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

      <button type="submit" className="auth-btn">
        Connexion
      </button>
      <p className="auth-message">
        <Link to="/register">Pas encore inscrit ?</Link>
      </p>
      {generalError && (
        <div aria-live="polite">
          <p className="auth-message auth-error">{generalError}</p>
        </div>
      )}
    </form>
  );
};

export default Login;
