<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\EventRegistrationController;

use App\Http\Controllers\FranchiseeController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\TruckMaintenanceController;

use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\StockOrderController;
use App\Http\Controllers\StockOrderItemController;

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;

use App\Http\Controllers\LoyaltyCardController;
use App\Http\Controllers\NewsletterLogController;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CustomerFeedbackController;
use App\Http\Controllers\NewsletterController;

// Test route (without auth)
Route::get('/test', function () {
    return response()->json(['message' => 'API works']);
});

// Routes without auth for testing
Route::apiResource('warehouses', WarehouseController::class);
Route::apiResource('trucks', TruckController::class);

// Authentification
Route::post('/login', [AuthController::class, 'login']);
Route::post('/customer/login', [CustomerAuthController::class, 'login']);

// Routes accessibles après authentification
Route::middleware(['auth:sanctum'])->group(function () {
    // Franchisees & Trucks
    Route::apiResource('franchisees', FranchiseeController::class);
    Route::apiResource('truck-maintenances', TruckMaintenanceController::class);

    // Stock
    Route::apiResource('stock-items', StockItemController::class);
    Route::apiResource('stock-orders', StockOrderController::class);
    Route::apiResource('stock-order-items', StockOrderItemController::class);

    // Menus & commandes clients
    Route::apiResource('menus', MenuController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('order-items', OrderItemController::class);

    // Fidélité & newsletter
    Route::apiResource('loyalty-cards', LoyaltyCardController::class)->only(['index', 'show', 'update']);
    Route::apiResource('newsletter-logs', NewsletterLogController::class)->only(['index', 'store']);

    // Routes génériques
    Route::get('/profile', fn (Request $request) => $request->user());

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);

    // Espace Admin
    Route::get('/admin/dashboard', fn () => response()->json(['message' => 'Bienvenue Admin']));

    // Génération de PDF pour les franchisees
    Route::get('/franchisees/{id}/pdf', [FranchiseeController::class, 'generatePdf']);

    // Événements (Client)
    Route::get('/my-events', [EventRegistrationController::class, 'myEvents']);
    Route::post('/events/{event}/register', [EventRegistrationController::class, 'register']);
    Route::delete('/events/{event}/unregister', [EventRegistrationController::class, 'unregister']);
    Route::get('/events/{event}/participants', [EventRegistrationController::class, 'eventParticipants']);

    // CLIENT
    Route::apiResource('customers', CustomerController::class)->only(['index', 'update', 'destroy']);
    Route::apiResource('customer-orders', CustomerOrderController::class)->only(['index', 'store', 'show']);
    Route::apiResource('feedback', CustomerFeedbackController::class)->only(['store']);
  


});

    // Public
    Route::apiResource('menus', MenuController::class)->only(['index', 'show']);
    Route::apiResource('dishes', DishController::class)->only(['index', 'show']);
    Route::apiResource('events', EventController::class)->only(['index']);
    Route::post('/customers', [CustomerController::class, 'store']); // inscription client