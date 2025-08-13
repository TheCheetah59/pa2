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
        <label>Nom</label>
        <input
          className="auth-input"
          name="name"
          value={form.name}
          onChange={handleChange}
        />
        {errors.name && (
          <small className="auth-message auth-error">{errors.name[0]}</small>
        )}
      </div>
      <div className="auth-field">
        <label>Email</label>
        <input
          className="auth-input"
          name="email"
          value={form.email}
          onChange={handleChange}
        />
        {errors.email && (
          <small className="auth-message auth-error">{errors.email[0]}</small>
        )}
      </div>
      <div className="auth-field">
        <label>Mot de passe</label>
        <input
          className="auth-input"
          type="password"
          name="password"
          value={form.password}
          onChange={handleChange}
        />
        {errors.password && (
          <small className="auth-message auth-error">
            {errors.password[0]}
          </small>
        )}
      </div>
      <div className="auth-field">
        <label>Confirmez le mot de passe</label>
        <input
          className="auth-input"
          type="password"
          name="password_confirmation"
          value={form.password_confirmation}
          onChange={handleChange}
        />
        {errors.password_confirmation && (
          <small className="auth-message auth-error">
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
