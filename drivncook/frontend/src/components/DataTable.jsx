import React from "react";

const DataTable = ({ data = [], columns = [], onEdit, onDelete }) => {
  return (
    <table className="w-full border border-gray-300 mt-2">
      <thead>
        <tr>
          {columns.map((col) => (
            <th key={col.key} className="border px-2 py-1 text-left">
              {col.label}
            </th>
          ))}
          {(onEdit || onDelete) && (
            <th className="border px-2 py-1 text-left">Actions</th>
          )}
        </tr>
      </thead>
      <tbody>
        {data.map((row) => (
          <tr key={row.id}>
            {columns.map((col) => (
              <td key={col.key} className="border px-2 py-1">
                {row[col.key]}
              </td>
            ))}
            {(onEdit || onDelete) && (
              <td className="border px-2 py-1 space-x-2">
                {onEdit && (
                  <button
                    type="button"
                    onClick={() => onEdit(row)}
                    className="text-blue-600 hover:underline"
                  >
                    Éditer
                  </button>
                )}
                {onDelete && (
                  <button
                    type="button"
                    onClick={() => onDelete(row)}
                    className="text-red-600 hover:underline"
                  >
                    Supprimer
                  </button>
                )}
              </td>
            )}
          </tr>
        ))}
      </tbody>
    </table>
  );
};

export default DataTable;
