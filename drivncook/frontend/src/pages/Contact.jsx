import React, { useState } from "react";
import api from "../axios.jsx";

function Contact() {
  const [form, setForm] = useState({ name: "", email: "", message: "" });
  const [errors, setErrors] = useState({});
  const [success, setSuccess] = useState("");
  const [generalError, setGeneralError] = useState("");

  const handleChange = (e) =>
    setForm({ ...form, [e.target.name]: e.target.value });

  const submit = async (e) => {
    e.preventDefault();
    setErrors({});
    setSuccess("");
    setGeneralError("");
    try {
      await api.post("/api/contact", form);
      setSuccess("Message envoyé !");
      setForm({ name: "", email: "", message: "" });
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {});
      } else {
        setGeneralError(
          err.response?.data?.message || "Une erreur est survenue"
        );
      }
    }
  };

  return (
    <form onSubmit={submit} className="auth-form">
      <div className="auth-field">
        <label htmlFor="contact-name">Nom</label>
        <input
          id="contact-name"
          className="auth-input"
          type="text"
          name="name"
          value={form.name}
          onChange={handleChange}
          required
          autoComplete="name"
          aria-describedby="contact-name-error"
        />
        {errors.name && (
          <div aria-live="polite">
            <small id="contact-name-error" className="auth-message auth-error">
              {errors.name[0]}
            </small>
          </div>
        )}
      </div>
      <div className="auth-field">
        <label htmlFor="contact-email">Email</label>
        <input
          id="contact-email"
          className="auth-input"
          type="email"
          name="email"
          value={form.email}
          onChange={handleChange}
          required
          autoComplete="email"
          aria-describedby="contact-email-error"
        />
        {errors.email && (
          <div aria-live="polite">
            <small id="contact-email-error" className="auth-message auth-error">
              {errors.email[0]}
            </small>
          </div>
        )}
      </div>
      <div className="auth-field">
        <label htmlFor="contact-message">Message</label>
        <textarea
          id="contact-message"
          className="auth-input"
          name="message"
          rows="5"
          value={form.message}
          onChange={handleChange}
          required
          aria-describedby="contact-message-error"
        />
        {errors.message && (
          <div aria-live="polite">
            <small
              id="contact-message-error"
              className="auth-message auth-error"
            >
              {errors.message[0]}
            </small>
          </div>
        )}
      </div>
      <button type="submit" className="auth-btn">
        Envoyer
      </button>
      {generalError && (
        <div aria-live="polite">
          <p className="auth-message auth-error">{generalError}</p>
        </div>
      )}
      {success && (
        <div aria-live="polite">
          <p className="auth-message auth-success">{success}</p>
        </div>
      )}
    </form>
  );
}

export default Contact;