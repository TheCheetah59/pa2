<?php

namespace App\Http\Controllers;

use App\Models\Franchisee;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReportController extends Controller
{
    // Rapport global : collection $franchisees
    public function franchiseesPdf()
    {
        $franchisees = Franchisee::with(['trucks.maintenances'])->orderBy('id')->get();

        $pdf = PDF::loadView('reports.franchisee-report', compact('franchisees'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('rapport-franchisees.pdf');
        // return $pdf->stream('rapport-franchisees.pdf'); // si tu préfères l’aperçu
    }

    // Rapport d’un seul franchisé : variable $franchisee
    public function franchiseePdf(int $id)
    {
        $franchisee = Franchisee::with(['trucks.maintenances'])->findOrFail($id);

        $pdf = PDF::loadView('reports.franchisee-detail', compact('franchisee'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download("rapport-franchisee-{$id}.pdf");
    }
}
