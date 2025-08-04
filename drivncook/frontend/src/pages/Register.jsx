import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../useAuth";

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
      navigate("/dashboard");
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {});
      }
    }
  };

  return (
    <form onSubmit={submit}>
      <div>
        <label>Nom</label>
        <input name="name" value={form.name} onChange={handleChange} />
        {errors.name && <small>{errors.name[0]}</small>}
      </div>
      <div>
        <label>Email</label>
        <input name="email" value={form.email} onChange={handleChange} />
        {errors.email && <small>{errors.email[0]}</small>}
      </div>
      <div>
        <label>Mot de passe</label>
        <input
          type="password"
          name="password"
          value={form.password}
          onChange={handleChange}
        />
        {errors.password && <small>{errors.password[0]}</small>}
      </div>
      <div>
        <label>Confirmez le mot de passe</label>
        <input
          type="password"
          name="password_confirmation"
          value={form.password_confirmation}
          onChange={handleChange}
        />
        {errors.password_confirmation && (
          <small>{errors.password_confirmation[0]}</small>
        )}
      </div>

      <button type="submit">S'inscrire</button>
      {success && <p>{success}</p>}
    </form>
  );
};

export default Register;
