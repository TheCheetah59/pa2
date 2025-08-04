import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "../useAuth";

const Login = () => {
  const { login } = useAuth();
  const navigate = useNavigate();
  const [form, setForm] = useState({ email: "", password: "" });
  const [errors, setErrors] = useState({});
  const [success, setSuccess] = useState("");

  const handleChange = (e) =>
    setForm({ ...form, [e.target.name]: e.target.value });

  const submit = async (e) => {
    e.preventDefault();
    setErrors({});
    try {
      await login(form);
      setSuccess("Connexion réussie !");
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

      <button type="submit">Connexion</button>
      {success && <p>{success}</p>}
    </form>
  );
};

export default Login;
