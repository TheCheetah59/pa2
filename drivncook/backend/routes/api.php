<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\EventRegistrationController;

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
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Routes publiques (sans authentification)
|--------------------------------------------------------------------------
*/

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API works']);
});

// Routes publiques pour consultation
Route::apiResource('menus', MenuController::class)->only(['index', 'show']);
Route::apiResource('dishes', DishController::class)->only(['index', 'show']);
Route::apiResource('events', EventController::class)->only(['index']);

// Routes sans auth pour testing (à supprimer en production)
Route::apiResource('warehouses', WarehouseController::class);
Route::apiResource('trucks', TruckController::class);

// Inscription client (publique)
Route::post('/customers', [CustomerController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Authentification
|--------------------------------------------------------------------------
*/

// Login admin/staff
Route::post('/login', [AuthController::class, 'login']);

// Register admin/staff
Route::post('/register', [AuthController::class, 'register']);

// Account activation
Route::get('/activate/{token}', [AuthController::class, 'activate']);


// Login client
Route::post('/customer/login', [CustomerAuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Routes ADMIN/STAFF (auth:sanctum - utilise le modèle User)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Profil admin/staff
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', [AuthController::class, 'me']);

    
    // Déconnexion admin/staff
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Dashboard admin
    Route::get('/admin/dashboard', fn () => response()->json(['message' => 'Bienvenue Admin']));
    
    // Gestion des franchisés
    Route::apiResource('franchisees', FranchiseeController::class);
    Route::get('/franchisees/{id}/pdf', [FranchiseeController::class, 'generatePdf']);
    
    // Gestion des camions
    Route::apiResource('truck-maintenances', TruckMaintenanceController::class);
    
    // Gestion du stock
    Route::apiResource('stock-items', StockItemController::class);
    Route::apiResource('stock-orders', StockOrderController::class);
    Route::apiResource('stock-order-items', StockOrderItemController::class);

    
    // Gestion des ventes
    Route::apiResource('sales', SaleController::class);
    Route::get('/sales/pdf', [SaleController::class, 'generatePdf']);
    
    // Gestion des utilisateurs
    Route::get('/users', [UserController::class, 'index']);
    Route::patch('/users/{user}/activate', [UserController::class, 'activate']);
    Route::patch('/users/{user}/suspend', [UserController::class, 'suspend']);
    
    // Gestion des menus (admin)
    Route::apiResource('menus', MenuController::class)->except(['index', 'show']);
    Route::apiResource('dishes', DishController::class)->except(['index', 'show']);
    
    // Gestion des commandes (admin)
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('order-items', OrderItemController::class);
    
    // Gestion des cartes de fidélité (admin)
    Route::apiResource('loyalty-cards', LoyaltyCardController::class);
    
    // Newsletter (admin)
    Route::apiResource('newsletter-logs', NewsletterLogController::class)->only(['index']);
    Route::post('/newsletters/send', [NewsletterController::class, 'send']);
    
    // Gestion des événements (admin)
    Route::apiResource('events', EventController::class)->except(['index']);
    Route::get('/events/{event}/participants', [EventRegistrationController::class, 'eventParticipants']);
    
    // Gestion des clients (admin)
    Route::apiResource('customers', CustomerController::class)->only(['index']);
    
});

/*
|--------------------------------------------------------------------------
| Routes CLIENT (auth:customer - utilise le modèle Customer)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:customer'])->group(function () {
    
    // Profile client
    Route::get('/customer/profile', fn (Request $request) => $request->user('customer'));
    
    // Déconnexion client
    Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);
    
    // Gestion du compte client
    Route::apiResource('customers', CustomerController::class)->only(['show', 'update', 'destroy']);
    
    // Commandes du client
    Route::apiResource('customer-orders', CustomerOrderController::class)->only(['index', 'store', 'show']);
    
    // Carte de fidélité du client
    Route::get('/my-loyalty-card', [LoyaltyCardController::class, 'show']);
    Route::put('/my-loyalty-card', [LoyaltyCardController::class, 'update']);
    
    // Événements du client
    Route::get('/my-events', [EventRegistrationController::class, 'myEvents']);
    Route::post('/events/{event}/register', [EventRegistrationController::class, 'register']);
    Route::delete('/events/{event}/unregister', [EventRegistrationController::class, 'unregister']);
    
    // Feedback client
    Route::apiResource('feedback', CustomerFeedbackController::class)->only(['store']);
    
    // Newsletter (inscription client)
    Route::apiResource('newsletter-logs', NewsletterLogController::class)->only(['store']);
    
});