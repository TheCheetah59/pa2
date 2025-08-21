import { useEffect, useState } from "react";
import { Elements } from "@stripe/react-stripe-js";
import { loadStripe } from "@stripe/stripe-js";
import CheckoutForm from "../components/payments/CheckoutForm";

const stripePromise = loadStripe(import.meta.env.VITE_STRIPE_KEY);

const Checkout = () => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    stripePromise
      .then(() => setLoading(false))
      .catch((err) => {
        console.error(err);
        setError("Impossible de charger le module de paiement.");
        setLoading(false);
      });
  }, []);

  if (loading) {
    return <p>Chargement...</p>;
  }

  if (error) {
    return <p className="text-red-600">{error}</p>;
  }

  return (
    <Elements stripe={stripePromise}>
      <CheckoutForm />
    </Elements>
  );
};

export default Checkout;
