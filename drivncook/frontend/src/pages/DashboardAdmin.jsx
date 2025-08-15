import React, { useEffect, useState } from "react";
import api from "../axios.jsx";

const DashboardAdmin = () => {
  const [users, setUsers] = useState([]);

  useEffect(() => {
    const fetchUsers = async () => {
      try {
        const { data } = await api.get("/api/admin/users");
        setUsers(Array.isArray(data) ? data.filter((u) => !u.is_activated) : []);
      } catch (err) {
        console.error(err);
      }
    };
    fetchUsers();
  }, []);

  const handleAction = async (id, action) => {
    try {
      await api.post(`/api/admin/users/${id}/${action}`);
      setUsers((prev) => prev.filter((u) => u.id !== id));
    } catch (err) {
      console.error(err);
    }
  };

  return (
    <div className="p-4">
      <h1>Dashboard Admin</h1>
      <ul>
        {users.map((u) => (
          <li key={u.id} className="mb-2">
            {u.name} ({u.email})
            <button
              className="ml-2 mr-1 px-2 py-1 bg-green-500 text-white"
              onClick={() => handleAction(u.id, "activate")}
            >
              Activer
            </button>
            <button
              className="px-2 py-1 bg-red-500 text-white"
              onClick={() => handleAction(u.id, "suspend")}
            >
              Suspendre
            </button>
          </li>
        ))}
      </ul>
    </div>
  );
};

export default DashboardAdmin;