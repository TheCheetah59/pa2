<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Franchisee;

// --- Sanctum + Auth (session-based) ---
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ActivationController;

/*
|--------------------------------------------------------------------------
| Routes WEB (middleware "web")
| - CSRF cookie Sanctum
| - Auth /login /logout /register sous session
| - PDF
| - Catch-all SPA en dernier
|--------------------------------------------------------------------------
*/

// 1) CSRF cookie (appelé depuis le front avant /login)
Route::get('/sanctum/csrf-cookie', fn () => response()->noContent());

// 2) Auth admin/staff via session Sanctum
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); // si utilisé
Route::post('/logout',   [AuthController::class, 'logout'])->middleware('auth');

// 3) Activation de compte
Route::get('/activate/{token}', [ActivationController::class, 'index']);

// 4) PDF franchisés
Route::get('/franchisees/report/{id?}', function ($id = null) {
    if ($id) {
        $franchisee = Franchisee::findOrFail($id);
        $franchisees = collect([$franchisee]);
        $filename = "rapport-{$franchisee->franchise_code}.pdf";
    } else {
        $franchisees = Franchisee::all();
        $filename = 'rapport-tous-franchises.pdf';
    }
    $pdf = Pdf::loadView('franchisee-report', compact('franchisees'));
    return $pdf->stream($filename);
});

Route::get('/franchisee/code/{code}/report', function ($code) {
    $franchisee = Franchisee::where('franchise_code', $code)->firstOrFail();
    $franchisees = collect([$franchisee]);
    $pdf = Pdf::loadView('franchisee-report', compact('franchisees'));
    return $pdf->stream("rapport-{$code}.pdf");
});

// 5) Catch‑all pour la SPA (doit rester le DERNIER)
Route::get('{any}', function () {
    return response()->file(public_path('index.html')); // sert le bon Content-Type
})->where('any', '.*');
