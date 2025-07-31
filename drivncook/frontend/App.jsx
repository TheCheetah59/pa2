import React, { useEffect, useState } from "react";
import axios from "axios";
import { BrowserRouter as Router, Routes, Route, Link } from "react-router-dom";

function HomePage() {
  const [menus, setMenus] = useState([]);

  useEffect(() => {
    axios
      .get("http://localhost:8000/api/menus/1?lang=fr")
      .then((response) => setMenus([response.data]))
      .catch((error) => console.error(error));
  }, []);

  return (
    <div className="p-8">
      <h1 className="text-3xl font-bold mb-4">Nos Menus</h1>
      {menus.map((menu) => (
        <div key={menu.id} className="mb-6">
          <h2 className="text-xl font-semibold">{menu.name}</h2>
          <p className="text-gray-600 mb-2">{menu.description}</p>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {menu.dishes.map((dish) => (
              <div key={dish.id} className="border rounded-xl p-4 shadow">
                <h3 className="text-lg font-bold">{dish.name}</h3>
                <p>{dish.description}</p>
                <p className="text-green-700 font-semibold mt-2">
                  {dish.price} €
                </p>
                {dish.image_url && (
                  <img
                    src={dish.image_url}
                    alt={dish.name}
                    className="mt-2 rounded-lg w-full h-40 object-cover"
                  />
                )}
              </div>
            ))}
          </div>
        </div>
      ))}
    </div>
  );
}

export default function App() {
  return (
    <Router>
      <nav className="bg-gray-800 text-white p-4">
        <Link to="/" className="mr-4">
          Accueil
        </Link>
        <Link to="/login">Connexion</Link>
        <Link to="/register">Inscription</Link>
      </nav>
      <Routes>
        <Route path="/" element={<HomePage />} />
        {/* Autres routes ici plus tard */}
      </Routes>
    </Router>
  );
}
