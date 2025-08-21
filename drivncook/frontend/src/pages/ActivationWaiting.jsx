// src/pages/ActivationWaiting.jsx
import { useEffect, useMemo, useState } from "react";
import { useLocation } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx"; // expose resendActivation(email)
import "./styles/Auth.css"; // ou ton css global

export default function ActivationWaiting() {
  const { resendActivation } = useAuth();
  const location = useLocation();

  const initialEmail =
    (location.state && location.state.email) ||
    (typeof sessionStorage !== "undefined" &&
      sessionStorage.getItem("pending_email")) ||
    "";

  const [email, setEmail] = useState(initialEmail);
  const [submitting, setSubmitting] = useState(false);
  const [msg, setMsg] = useState(null); // {type:'success'|'error', text:string}
  const [secondsLeft, setSecondsLeft] = useState(0); // cooldown

  // Sauvegarde légère de l'email pour rechargements/retours
  useEffect(() => {
    try {
      sessionStorage.setItem("pending_email", email);
    } catch {}
  }, [email]);

  const isValidEmail = useMemo(
    () => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim()),
    [email]
  );

  useEffect(() => {
    if (secondsLeft <= 0) return;
    const t = setInterval(() => setSecondsLeft((s) => s - 1), 1000);
    return () => clearInterval(t);
  }, [secondsLeft]);

  const onSubmit = async (e) => {
    e.preventDefault();
    setMsg(null);

    if (!isValidEmail) {
      setMsg({ type: "error", text: "Adresse email invalide." });
      return;
    }

    setSubmitting(true);
    try {
      const { message } = await resendActivation(email.trim());
      setMsg({
        type: "success",
        text:
          message ||
          "Si un compte existe et n'est pas vérifié, un email vient d’être renvoyé.",
      });
      // démarre un cooldown pour limiter les renvois
      setSecondsLeft(60);
    } catch (err) {
      const status = err?.response?.status;
      if (status === 429) {
        setMsg({
          type: "error",
          text: "Trop de demandes. Réessaie un peu plus tard.",
        });
        setSecondsLeft(60);
      } else {
        setMsg({
          type: "error",
          text:
            err?.response?.data?.message ||
            "Impossible d'envoyer la demande pour le moment.",
        });
      }
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="max-w-md mx-auto p-6">
      <h1 className="text-2xl font-semibold mb-2">Vérifie ton email</h1>
      <p className="mb-4">
        Un lien d’activation t’a été envoyé. Si tu ne l’as pas reçu, tu peux
        demander un nouvel envoi.
      </p>

      {msg && (
        <div
          className={`auth-message ${
            msg.type === "error" ? "auth-error" : "auth-success"
          }`}
          role="status"
          aria-live="polite"
          style={{ marginBottom: 12 }}
        >
          {msg.text}
        </div>
      )}

      <form onSubmit={onSubmit} className="space-y-3" noValidate>
        <div className="auth-field">
          <label htmlFor="resend-email">Email</label>
          <input
            id="resend-email"
            className={`auth-input ${
              !isValidEmail && email ? "auth-input-error" : ""
            }`}
            type="email"
            name="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            autoComplete="email"
            aria-describedby={
              !isValidEmail && email ? "resend-email-error" : undefined
            }
            disabled={submitting || secondsLeft > 0}
          />
          {!isValidEmail && email && (
            <small id="resend-email-error" className="auth-message auth-error">
              Saisis une adresse valide.
            </small>
          )}
        </div>

        <button
          type="submit"
          className="auth-btn"
          disabled={submitting || !isValidEmail || secondsLeft > 0}
        >
          {secondsLeft > 0
            ? `Renvoyer dans ${secondsLeft}s`
            : submitting
            ? "Envoi..."
            : "Renvoyer le lien d’activation"}
        </button>
      </form>
    </div>
  );
}
