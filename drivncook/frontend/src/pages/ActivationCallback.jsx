import React, { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import api from "../axios";

const ActivationCallback = () => {
  const { token } = useParams();
  const navigate = useNavigate();
  const [error, setError] = useState("");

  useEffect(() => {
    const activate = async () => {
      try {
        await api.get(`/api/activate/${token}`);
        navigate("/login?activated=1", { replace: true });
      } catch (err) {
        setError(err.response?.data?.message || "Une erreur est survenue");
      }
    };
    activate();
  }, [token, navigate]);

  return (
    <div className="activation-callback">
      {error ? <p>{error}</p> : <p>Activation en cours...</p>}
    </div>
  );
};

export default ActivationCallback;
