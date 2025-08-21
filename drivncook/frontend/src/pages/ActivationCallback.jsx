// src/pages/ActivationCallback.jsx
import { useMemo, useEffect } from "react";
import { useSearchParams, Link, useNavigate } from "react-router-dom";

export default function ActivationCallback() {
  const [params] = useSearchParams();
  const navigate = useNavigate();

  const status = params.get("status"); // peut être null
  const success = useMemo(() => status === "verified", [status]);

  // Optionnel: redirection automatique vers /login après 1,2s
  useEffect(() => {
    if (success) {
      const t = setTimeout(() => navigate("/login", { replace: true }), 1200);
      return () => clearTimeout(t);
    }
  }, [success, navigate]);

  return (
    <div className="max-w-md mx-auto p-6">
      {success ? (
        <>
          <h1 className="text-2xl font-semibold mb-2">
            Ton email est vérifié ✅
          </h1>
          <p className="mb-4">Tu peux maintenant te connecter.</p>
          <Link to="/login" className="underline">
            Aller à la connexion
          </Link>
        </>
      ) : (
        <>
          <h1 className="text-2xl font-semibold mb-2">Vérification échouée</h1>
          <p className="mb-4">
            Lien invalide ou expiré. Demande un nouvel envoi.
          </p>
          <Link to="/activation/waiting" className="underline">
            Renvoyer le lien
          </Link>
        </>
      )}
    </div>
  );
}
