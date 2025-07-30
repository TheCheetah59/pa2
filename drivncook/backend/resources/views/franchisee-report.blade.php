<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport des Franchisés</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        
        .header-info {
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            font-size: 11px;
        }
        
        /* Largeurs spécifiques pour chaque colonne */
        th:nth-child(1), td:nth-child(1) { width: 8%; text-align: center; } /* ID */
        th:nth-child(2), td:nth-child(2) { width: 15%; } /* Nom */
        th:nth-child(3), td:nth-child(3) { width: 22%; font-size: 10px; } /* Email */
        th:nth-child(4), td:nth-child(4) { width: 12%; text-align: center; } /* Téléphone */
        th:nth-child(5), td:nth-child(5) { width: 12%; text-align: center; } /* Ville */
        th:nth-child(6), td:nth-child(6) { width: 10%; text-align: center; } /* Code */
        th:nth-child(7), td:nth-child(7) { width: 11%; text-align: center; } /* Frais */
        th:nth-child(8), td:nth-child(8) { width: 10%; text-align: center; } /* % */
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            color: white;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
            min-width: 50px;
            text-align: center;
        }
        
        .status.paid {
            background-color: #27ae60;
        }
        
        .status.unpaid {
            background-color: #e74c3c;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Rapport des Franchisés DrivnCook</h1>
    
    <div class="header-info">
        <p><strong>Date du rapport :</strong> {{ now()->format('Y-m-d H:i:s ') }}</p>
        <p><strong>Nombre total de franchisés :</strong> {{ count($franchisees) }}</p>
    </div>

    @if($franchisees->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Ville</th>
                    <th>Code<br>Franchise</th>
                    <th>Frais<br>d'entrée</th>
                    <th>%<br>Ventes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($franchisees as $franchisee)
                <tr>
                    <td>{{ $franchisee->id }}</td>
                    <td>{{ $franchisee->name }}</td>
                    <td>{{ $franchisee->email }}</td>
                    <td>{{ $franchisee->phone }}</td>
                    <td>{{ $franchisee->city }}</td>
                    <td>{{ $franchisee->franchise_code }}</td>
                    <td>
                        @if($franchisee->entry_fee_paid)
                            <span class="status paid">Payé</span>
                        @else
                            <span class="status unpaid">Non payé</span>
                        @endif
                    </td>
                    <td>{{ $franchisee->sales_percentage }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #666; font-style: italic;">
            Aucun franchisé trouvé dans la base de données.
        </p>
    @endif

    <div class="footer">
        <p>Rapport généré automatiquement par DrivnCook - {{ now()->format('Y') }}</p>
    </div>
</body>
</html>