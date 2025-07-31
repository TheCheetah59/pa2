<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport des Ventes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
        h1 { text-align: center; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px 4px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; text-align: center; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Historique des Ventes</h1>
    <p style="text-align: center; font-size: 14px;">Date du rapport : {{ now()->format('Y-m-d H:i:s') }}</p>
    @if($sales->count() > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Franchise</th>
                <th>Date</th>
                <th>Produit</th>
                <th>Qté</th>
                <th>PU</th>
                <th>Total</th>
                <th>Paiement</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->id }}</td>
                <td>{{ $sale->franchisee->franchise_code ?? $sale->franchisee_id }}</td>
                <td>{{ $sale->sale_date }}</td>
                <td>{{ $sale->product_name }}</td>
                <td>{{ $sale->quantity_sold }}</td>
                <td>{{ number_format($sale->unit_price,2) }}€</td>
                <td>{{ number_format($sale->total_price,2) }}€</td>
                <td>{{ $sale->payment_method }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p style="text-align: center;">Aucune vente enregistrée.</p>
    @endif
</body>
</html>