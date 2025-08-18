import React, { useEffect, useState } from "react";
import api from "../axios.jsx";

const DashboardClient = () => {
  const [profile, setProfile] = useState(null);
  const [orders, setOrders] = useState([]);
  const [loyaltyCard, setLoyaltyCard] = useState(null);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [profileRes, ordersRes, loyaltyRes] = await Promise.all([
          api.get("/api/customer/profile"),
          api.get("/api/customer-orders"),
          api.get("/api/my-loyalty-card"),
        ]);

        setProfile(profileRes.data);
        setOrders(Array.isArray(ordersRes.data) ? ordersRes.data : []);
        setLoyaltyCard(loyaltyRes.data);
      } catch (err) {
        console.error(err);
      }
    };

    fetchData();
  }, []);

  return (
    <div className="p-4">
      <h1>Dashboard Client</h1>

      <section className="mt-4">
        <h2>Profil</h2>
        {profile ? (
          <ul>
            <li>Nom : {profile.name}</li>
            <li>Email : {profile.email}</li>
          </ul>
        ) : (
          <p>Chargement du profil...</p>
        )}
      </section>

      <section className="mt-4">
        <h2>Commandes</h2>
        {orders.length > 0 ? (
          <ul>
            {orders.map((order) => (
              <li key={order.id}>
                Commande #{order.id} - {order.status}
              </li>
            ))}
          </ul>
        ) : (
          <p>Aucune commande.</p>
        )}
      </section>

      <section className="mt-4">
        <h2>Carte de fidélité</h2>
        {loyaltyCard ? (
          <p>Points : {loyaltyCard.points}</p>
        ) : (
          <p>Chargement de la carte de fidélité...</p>
        )}
      </section>
    </div>
  );
};

export default DashboardClient;
