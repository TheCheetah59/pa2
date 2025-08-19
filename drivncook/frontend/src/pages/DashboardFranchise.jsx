import React, { useEffect, useState } from "react";
import api from "../axios.jsx";

/**
 * Besoins du dashboard franchise :
 * - Visualiser et gérer les camions de la franchise.
 * - Consulter le stock des produits disponibles.
 */
const DashboardFranchise = () => {
  const [trucks, setTrucks] = useState([]);
  const [stockItems, setStockItems] = useState([]);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [trucksRes, stockRes] = await Promise.all([
          api.get("/api/trucks"),
          api.get("/api/stock-items"),
        ]);
        setTrucks(Array.isArray(trucksRes.data) ? trucksRes.data : []);
        setStockItems(Array.isArray(stockRes.data) ? stockRes.data : []);
      } catch (err) {
        console.error(err);
      }
    };
    fetchData();
  }, []);

  return (
    <div className="p-4">
      <h1>Dashboard Franchise</h1>

      <section className="mt-4">
        <h2>Gestion des camions</h2>
        {trucks.length > 0 ? (
          <ul>
            {trucks.map((truck) => (
              <li key={truck.id}>
                {truck.plate_number} - {truck.status}
              </li>
            ))}
          </ul>
        ) : (
          <p>Aucun camion enregistré.</p>
        )}
      </section>

      <section className="mt-4">
        <h2>Gestion du stock</h2>
        {stockItems.length > 0 ? (
          <ul>
            {stockItems.map((item) => (
              <li key={item.id}>
                {item.name} - {item.stock_quantity}
              </li>
            ))}
          </ul>
        ) : (
          <p>Aucun article en stock.</p>
        )}
      </section>
    </div>
  );
};

export default DashboardFranchise;
