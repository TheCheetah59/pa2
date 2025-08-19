import React, { useEffect, useState } from "react";
import api from "../axios";

const Menu = () => {
  const [menus, setMenus] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchMenus = async () => {
      try {
        const response = await api.get("/api/menus");
        setMenus(Array.isArray(response.data) ? response.data : []);
      } catch {
        setError("Erreur lors du chargement du menu");
      } finally {
        setLoading(false);
      }
    };

    fetchMenus();
  }, []);

  if (loading) {
    return <div className="p-4 text-center">Chargement...</div>;
  }

  if (error) {
    return <div className="p-4 text-center text-red-500">{error}</div>;
  }

  return (
    <div className="container mx-auto p-4">
      <h1 className="mb-4 text-center text-2xl font-bold">Menu</h1>
      <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {menus.map((menu) => (
          <div
            key={menu.id}
            className="flex flex-col rounded-lg bg-white p-6 shadow"
          >
            <h2 className="mb-2 text-xl font-semibold">{menu.name}</h2>
            {menu.description && (
              <p className="mb-4 flex-grow text-gray-600">{menu.description}</p>
            )}
            <p className="mt-auto text-lg font-bold">{menu.price} €</p>
          </div>
        ))}
      </div>
    </div>
  );
};

export default Menu;
