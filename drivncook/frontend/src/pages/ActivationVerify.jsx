// src/pages/ActivationVerify.jsx
import { useEffect } from "react";
import { useNavigate, useParams, useSearchParams } from "react-router-dom";
import api from "../axios.jsx";

export default function ActivationVerify() {
  const { id, hash } = useParams();
  const [params] = useSearchParams();
  const navigate = useNavigate();

  useEffect(() => {
    const verify = async () => {
      try {
        const query = params.toString();
        const url = `/email/verify/${id}/${hash}${query ? `?${query}` : ""}`;
        await api.get(url, { headers: { Accept: "application/json" } });
        navigate("/activation/callback?status=verified", { replace: true });
      } catch {
        navigate("/activation/callback?status=error", { replace: true });
      }
    };
    verify();
  }, [id, hash, params, navigate]);

  return <p className="text-center mt-4">Vérification en cours...</p>;
}
