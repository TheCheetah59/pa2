<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Franchisee;

Route::get('/franchisees/report/{id?}', function ($id = null) {
    if ($id) {
        $franchisee = Franchisee::findOrFail($id);
        $franchisees = collect([$franchisee]);
        $filename = "rapport-{$franchisee->franchise_code}.pdf";
    } else {
        $franchisees = Franchisee::all();
        $filename = 'rapport-tous-franchises.pdf';
    }
    
    // dd() retiré
    $pdf = Pdf::loadView('franchisee-report', compact('franchisees'));
    return $pdf->stream($filename);
});

// PDF par code franchise
Route::get('/franchisee/code/{code}/report', function ($code) {
    $franchisee = Franchisee::where('franchise_code', $code)->firstOrFail();
    $franchisees = collect([$franchisee]);
    $pdf = Pdf::loadView('franchisee-report', compact('franchisees'));
    return $pdf->stream("rapport-{$code}.pdf");


});

Route::get('{any}', fn () => file_get_contents(public_path('index.html')))
     ->where('any', '.*');