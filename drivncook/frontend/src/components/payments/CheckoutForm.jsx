import { useState } from "react";
import { useStripe, useElements, CardElement } from "@stripe/react-stripe-js";
import api from "../../axios.jsx";

const CheckoutForm = ({ orderId, onSuccess }) => {
  const stripe = useStripe();
  const elements = useElements();

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!stripe || !elements) return;

    setError("");
    setLoading(true);

    try {
      const { data } = await api.post(`/orders/${orderId}/payment-intent`);
      const clientSecret = data?.client_secret;
      if (!clientSecret) throw new Error("Missing client secret");

      const card = elements.getElement(CardElement);
      const { error: stripeError, paymentIntent } =
        await stripe.confirmCardPayment(clientSecret, {
          payment_method: {
            card,
          },
        });

      if (stripeError) {
        setError(stripeError.message || "Payment failed");
        return;
      }

      if (paymentIntent?.status === "succeeded") {
        await api.post(`/orders/${orderId}/confirm-payment`);
        if (onSuccess) onSuccess(paymentIntent);
      } else {
        setError("Payment not completed.");
      }
    } catch (err) {
      setError(err?.response?.data?.message || err.message || "Payment failed");
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <CardElement
        onChange={(e) => (e.error ? setError(e.error.message) : setError(""))}
      />
      {error && (
        <p className="text-red-500 text-sm" role="alert">
          {error}
        </p>
      )}
      <button
        type="submit"
        disabled={!stripe || !elements || loading}
        className="bg-blue-500 text-white px-4 py-2 rounded disabled:opacity-50"
      >
        {loading ? "Processing..." : "Pay"}
      </button>
    </form>
  );
};

export default CheckoutForm;
