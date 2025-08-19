import React, { useEffect, useState } from "react";
import api from "../axios.jsx";
import DataTable from "../components/DataTable.jsx";
import SimpleForm from "../components/SimpleForm.jsx";

/**
 * Besoins du dashboard franchise :
 * - Visualiser et gérer les camions de la franchise.
 * - Consulter le stock des produits disponibles.
 */
const DashboardFranchise = () => {
  const [trucks, setTrucks] = useState([]);
  const [stockItems, setStockItems] = useState([]);
  const [editingTruck, setEditingTruck] = useState(null);
  const [editingStock, setEditingStock] = useState(null);
  const [feedback, setFeedback] = useState(null);

  const truckColumns = [
    { key: "plate_number", label: "Plaque" },
    { key: "status", label: "Statut" },
  ];

  const truckFields = [
    { name: "plate_number", label: "Plaque" },
    { name: "status", label: "Statut" },
  ];

  const stockColumns = [
    { key: "name", label: "Nom" },
    { key: "stock_quantity", label: "Quantité" },
  ];

  const stockFields = [
    { name: "name", label: "Nom" },
    { name: "stock_quantity", label: "Quantité" },
  ];

  const showFeedback = (type, text) => {
    setFeedback({ type, text });
    setTimeout(() => setFeedback(null), 4000);
  };

  const fetchTrucks = async () => {
    try {
      const res = await api.get("/api/trucks");
      setTrucks(Array.isArray(res.data) ? res.data : []);
    } catch (err) {
      console.error(err);
      showFeedback("error", "Erreur lors du chargement des camions");
    }
  };

  const fetchStockItems = async () => {
    try {
      const res = await api.get("/api/stock-items");
      setStockItems(Array.isArray(res.data) ? res.data : []);
    } catch (err) {
      console.error(err);
      showFeedback("error", "Erreur lors du chargement du stock");
    }
  };

  useEffect(() => {
    fetchTrucks();
    fetchStockItems();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const saveTruck = async (data) => {
    try {
      if (data.id) {
        await api.put(`/api/trucks/${data.id}`, data);
      } else {
        await api.post("/api/trucks", data);
      }
      setEditingTruck(null);
      fetchTrucks();
      showFeedback("success", "Camion enregistré");
    } catch (err) {
      console.error(err);
      showFeedback("error", "Erreur lors de l'enregistrement du camion");
    }
  };

  const deleteTruck = async (truck) => {
    if (!window.confirm("Supprimer ce camion ?")) return;
    try {
      await api.delete(`/api/trucks/${truck.id}`);
      fetchTrucks();
      showFeedback("success", "Camion supprimé");
    } catch (err) {
      console.error(err);
      showFeedback("error", "Erreur lors de la suppression du camion");
    }
  };

  const saveStock = async (data) => {
    try {
      const payload = { ...data, stock_quantity: Number(data.stock_quantity) };
      if (payload.id) {
        await api.put(`/api/stock-items/${payload.id}`, payload);
      } else {
        await api.post("/api/stock-items", payload);
      }
      setEditingStock(null);
      fetchStockItems();
      showFeedback("success", "Article de stock enregistré");
    } catch (err) {
      console.error(err);
      showFeedback("error", "Erreur lors de l'enregistrement du stock");
    }
  };

  const deleteStock = async (item) => {
    if (!window.confirm("Supprimer cet article ?")) return;
    try {
      await api.delete(`/api/stock-items/${item.id}`);
      fetchStockItems();
      showFeedback("success", "Article supprimé");
    } catch (err) {
      console.error(err);
      showFeedback("error", "Erreur lors de la suppression de l'article");
    }
  };

  return (
    <div className="p-4">
      <h1>Dashboard Franchise</h1>

      {feedback && (
        <p
          className={`mt-2 ${
            feedback.type === "error" ? "text-red-600" : "text-green-600"
          }`}
        >
          {feedback.text}
        </p>
      )}

      <section className="mt-4">
        <h2>Gestion des camions</h2>
        <button
          type="button"
          onClick={() => setEditingTruck({ plate_number: "", status: "" })}
          className="bg-green-500 text-white px-2 py-1 mt-2"
        >
          Ajouter un camion
        </button>
        {trucks.length > 0 ? (
          <DataTable
            data={trucks}
            columns={truckColumns}
            onEdit={setEditingTruck}
            onDelete={deleteTruck}
          />
        ) : (
          <p>Aucun camion enregistré.</p>
        )}
        {editingTruck && (
          <SimpleForm
            initialData={editingTruck}
            fields={truckFields}
            onSubmit={saveTruck}
            onCancel={() => setEditingTruck(null)}
          />
        )}
      </section>

      <section className="mt-8">
        <h2>Gestion du stock</h2>
        <button
          type="button"
          onClick={() =>
            setEditingStock({ name: "", stock_quantity: 0 })
          }
          className="bg-green-500 text-white px-2 py-1 mt-2"
        >
          Ajouter un article
        </button>
        {stockItems.length > 0 ? (
          <DataTable
            data={stockItems}
            columns={stockColumns}
            onEdit={setEditingStock}
            onDelete={deleteStock}
          />
        ) : (
          <p>Aucun article en stock.</p>
        )}
        {editingStock && (
          <SimpleForm
            initialData={editingStock}
            fields={stockFields}
            onSubmit={saveStock}
            onCancel={() => setEditingStock(null)}
          />
        )}
      </section>
    </div>
  );
};

export default DashboardFranchise;