import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../useAuth";
import "./styles/Auth.css";

const Register = () => {
  const { register } = useAuth();
  const navigate = useNavigate();
  const [form, setForm] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  });
  const [errors, setErrors] = useState({});
  const [success, setSuccess] = useState("");

  const handleChange = (e) =>
    setForm({ ...form, [e.target.name]: e.target.value });

  const submit = async (e) => {
    e.preventDefault();
    setErrors({});
    try {
      await register(form);
      setSuccess("Inscription réussie !");
      navigate("/menu");
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {});
      }
    }
  };

  return (
    <form onSubmit={submit} className="auth-form">
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
          <small id="register-name-error" className="auth-message auth-error">
            {errors.name[0]}
          </small>
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
          <small id="register-email-error" className="auth-message auth-error">
            {errors.email[0]}
          </small>
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
          <small
            id="register-password-confirmation-error"
            className="auth-message auth-error"
          >
            {errors.password_confirmation[0]}
          </small>
        )}
      </div>

      <button type="submit" className="auth-btn">
        S'inscrire
      </button>
      {success && <p className="auth-message auth-success">{success}</p>}
    </form>
  );
};

export default Register;
