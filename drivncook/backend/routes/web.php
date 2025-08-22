<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Franchisee;

// --- Sanctum + Auth (session-based) ---
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerAuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Routes WEB (middleware "web")
| - CSRF cookie Sanctum
| - Auth /login /logout /register sous session
| - PDF
| - Vérification d'email
| - Catch-all SPA en dernier
|--------------------------------------------------------------------------
*/

// 1) CSRF cookie (appelé depuis le front avant /login)
Route::get('/sanctum/csrf-cookie', [\Laravel\Sanctum\Http\Controllers\CsrfCookieController::class, 'show']);

// 2) Auth admin/staff via session Sanctum
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// 3) Vérification d'email - VERSION MANUELLE SANS AUTH
Route::get('/email/verify/{id}/{hash}', function ($id, $hash, Request $request) {
    try {
        // Vérifier la signature manuellement
        if (! $request->hasValidSignature()) {
            abort(401, 'Invalid verification link');
        }
        
        // Trouver l'utilisateur par ID
        $user = \App\Models\User::find($id);
        
        if (! $user) {
            abort(404, 'User not found');
        }
        
        // Vérifier le hash
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            abort(401, 'Invalid verification hash');
        }
        
        // Marquer comme vérifié si pas déjà fait
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        
        // Nettoyage complet des caractères problématiques
        $front = env('APP_FRONT_URL', 'http://localhost:5173');
        $front = preg_replace("/[\r\n\t\s]+$/", '', $front);
        $front = rtrim($front, '/');
        
        return redirect()->to($front.'/activation/callback?status=verified');
        
    } catch (\Exception $e) {
        $front = env('APP_FRONT_URL', 'http://localhost:5173');
        $front = preg_replace("/[\r\n\t\s]+$/", '', $front);
        $front = rtrim($front, '/');
        
        return redirect()->to($front.'/activation/callback?status=error');
    }
})->middleware(['throttle:6,1'])->name('verification.verify');

// (Optionnel) Renvoyer l'email de vérification (user connecté)
Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user() && ! $request->user()->hasVerifiedEmail()) {
        $request->user()->sendEmailVerificationNotification();
    }
    return response()->json(['message' => 'Lien de vérification renvoyé si nécessaire.']);
})->middleware(['auth','throttle:6,1'])->name('verification.send');

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

// 5) Auth clients
Route::post('/customer/login',  [CustomerAuthController::class, 'login']);
Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])->middleware('auth:customer');
Route::post('/register',        [CustomerAuthController::class, 'register']);

// 6) Catch-all pour la SPA (doit rester le DERNIER)
Route::get('{any}', function () {
    return response()->file(public_path('index.html')); // sert le bon Content-Type
})->where('any', '.*');