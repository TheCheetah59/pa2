<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\EventRegistrationController;

// Authentification
Route::post('/login', [AuthController::class, 'login']);
Route::post('/customer/login', [CustomerAuthController::class, 'login']);

// Routes accessibles après authentification
Route::middleware(['auth:sanctum'])->group(function () {

    // Routes génériques
    Route::get('/profile', fn (Request $request) => $request->user());

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);

    // Espace Admin
    Route::get('/admin/dashboard', fn () => response()->json(['message' => 'Bienvenue Admin']));

    // Événements (Client)
    Route::get('/my-events', [EventRegistrationController::class, 'myEvents']);
    Route::post('/events/{event}/register', [EventRegistrationController::class, 'register']);
    Route::delete('/events/{event}/unregister', [EventRegistrationController::class, 'unregister']);
    Route::get('/events/{event}/participants', [EventRegistrationController::class, 'eventParticipants']);
});
