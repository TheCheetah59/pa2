import React, { useEffect, useState } from "react";

const SimpleForm = ({ initialData = {}, fields = [], onSubmit, onCancel }) => {
  const [formData, setFormData] = useState(initialData);

  useEffect(() => {
    setFormData(initialData);
  }, [initialData]);

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onSubmit(formData);
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-2 mt-2">
      {fields.map((field) => (
        <div key={field.name} className="flex flex-col">
          <label className="text-sm" htmlFor={field.name}>
            {field.label}
          </label>
          <input
            id={field.name}
            name={field.name}
            value={formData[field.name] || ""}
            onChange={handleChange}
            className="border p-1"
          />
        </div>
      ))}
      <div className="space-x-2">
        <button
          type="submit"
          className="bg-blue-500 text-white px-3 py-1 rounded"
        >
          Enregistrer
        </button>
        {onCancel && (
          <button
            type="button"
            onClick={onCancel}
            className="bg-gray-300 px-3 py-1 rounded"
          >
            Annuler
          </button>
        )}
      </div>
    </form>
  );
};

export default SimpleForm;
