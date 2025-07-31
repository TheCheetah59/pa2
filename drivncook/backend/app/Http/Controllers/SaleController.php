<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function index(): Collection
    {
        return Sale::with('franchisee')->get();
    }

    public function store(Request $request): Sale
    {
        $validated = $request->validate([
            'franchisee_id'   => 'required|exists:franchisees,id',
            'sale_date'       => 'required|date',
            'product_name'    => 'required|string',
            'quantity_sold'   => 'required|integer|min:1',
            'unit_price'      => 'required|numeric|min:0',
            'total_price'     => 'required|numeric|min:0',
            'payment_method'  => 'nullable|string',
            'location'        => 'nullable|string',
            'notes'           => 'nullable|string',
        ]);

        return Sale::create($validated);
    }

    public function show($id): Sale
    {
        return Sale::with('franchisee')->findOrFail($id);
    }

    public function update(Request $request, $id): Sale
    {
        $sale = Sale::findOrFail($id);

        $validated = $request->validate([
            'franchisee_id'   => 'sometimes|exists:franchisees,id',
            'sale_date'       => 'sometimes|date',
            'product_name'    => 'sometimes|string',
            'quantity_sold'   => 'sometimes|integer|min:1',
            'unit_price'      => 'sometimes|numeric|min:0',
            'total_price'     => 'sometimes|numeric|min:0',
            'payment_method'  => 'nullable|string',
            'location'        => 'nullable|string',
            'notes'           => 'nullable|string',
        ]);

        $sale->update($validated);
        return $sale;
    }

    public function destroy($id): Response
    {
        Sale::destroy($id);
        return response()->noContent();
    }

    public function generatePdf(): Response
    {
        $sales = Sale::with('franchisee')->get();
        $pdf = Pdf::loadView('sales-report.blade', ['sales' => $sales]);
        return $pdf->download('sales_report.pdf');
    }
}