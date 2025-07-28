<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport du franchisé</title>
    <style>
        body { font-family: sans-serif; }
        h1 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Rapport de {{ $franchisee->name }}</h1>
    <p>Email : {{ $franchisee->email }}</p>
    <p>Code Franchise : {{ $franchisee->franchise_code }}</p>

    <h2>Liste des camions</h2>
    <table>
        <thead>
            <tr>
                <th>Modèle</th>
                <th>Immatriculation</th>
                <th>Status</th>
                <th>Localisation</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($franchisee->trucks as $truck)
                <tr>
                    <td>{{ $truck->model }}</td>
                    <td>{{ $truck->plate_number }}</td>
                    <td>{{ $truck->status }}</td>
                    <td>{{ $truck->current_location }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
