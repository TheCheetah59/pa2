<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerAuthController; // Guard "customer"

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
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;

use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Routes publiques API (sans authentification)
|--------------------------------------------------------------------------
*/

if (config('debug.enabled')) {
    Route::get('/test', fn () => response()->json(['message' => 'API works']));
}

// Consultation publique
Route::apiResource('menus',  MenuController::class)->only(['index', 'show']);
Route::apiResource('dishes', DishController::class)->only(['index', 'show']);
Route::apiResource('events', EventController::class)->only(['index']);

// Inscription client (compte Customer)
Route::post('/customers', [CustomerController::class, 'store']);

// Formulaire de contact
Route::post('/contact', [ContactController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Auth SPA (Sanctum stateful) - Utilisateurs (guard sanctum)
|--------------------------------------------------------------------------
| NB: ces endpoints sont consommés par le front React.
*/

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);           // publique
    Route::post('/login',    [AuthController::class, 'login']);              // publique

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);               // user courant (admin/franchise)
        Route::get('/admin',   [AuthController::class, 'admin'])->middleware('can:admin-only');
    });
});

/*
|--------------------------------------------------------------------------
| Auth client (guard "customer")
|--------------------------------------------------------------------------
| Endpoints dédiés au compte client (site côté clients).
*/

Route::prefix('customer')->group(function () {
    Route::post('/login',  [CustomerAuthController::class, 'login']);        // publique

    Route::middleware('auth:customer')->group(function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout']);
        Route::get('/profile', fn (Request $request) => $request->user('customer'));
    });
});

/*
|--------------------------------------------------------------------------
| Routes ADMIN/STAFF (guard sanctum) -> Modèle User
|--------------------------------------------------------------------------
| Préfixe /admin pour éviter toute collision avec les routes publiques.
*/

Route::middleware(['auth:sanctum', 'activated', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        // Dashboard admin
        Route::get('/dashboard', fn () => response()->json(['message' => 'Bienvenue Admin']));

        // Gestion des franchisés
        Route::apiResource('franchisees', FranchiseeController::class);
        Route::get('/franchisees/{id}/pdf', [FranchiseeController::class, 'generatePdf']);

        // Camions + maintenances
        Route::apiResource('trucks', TruckController::class);
        Route::apiResource('truck-maintenances', TruckMaintenanceController::class);

        // Stock (entrepôts, items, commandes)
        Route::apiResource('warehouses',        WarehouseController::class);
        Route::apiResource('stock-items',       StockItemController::class);
        Route::apiResource('stock-orders',      StockOrderController::class);
        Route::apiResource('stock-order-items', StockOrderItemController::class);

        // Ventes
        Route::apiResource('sales', SaleController::class);
        Route::get('/sales/pdf', [SaleController::class, 'generatePdf']);

        // Utilisateurs
        Route::get('/users',                        [AdminController::class, 'index']);
        Route::patch('/users/{user}/activate',      [AdminController::class, 'activate']);
        Route::patch('/users/{user}/suspend',       [AdminController::class, 'suspend']);
        Route::patch('/users/{user}/make-admin',    [AdminController::class, 'makeAdmin']);

        // Menus / Plats (admin)
        Route::apiResource('menus',  MenuController::class)->except(['index', 'show']);
        Route::apiResource('dishes', DishController::class)->except(['index', 'show']);

        // Commandes (admin)
        Route::apiResource('orders',      OrderController::class);
        Route::apiResource('order-items', OrderItemController::class);

        // Cartes de fidélité (admin)
        Route::apiResource('loyalty-cards', LoyaltyCardController::class);

        // Newsletter (admin)
        Route::apiResource('newsletter-logs', NewsletterLogController::class)->only(['index']);
        Route::post('/newsletters/send', [NewsletterController::class, 'send']);

        // Événements (admin)
        Route::apiResource('events', EventController::class)->except(['index']);
        Route::get('/events/{event}/participants', [EventRegistrationController::class, 'eventParticipants']);

        // Clients (admin)
        Route::apiResource('customers', CustomerController::class)->only(['index']);

        // Rapports PDF (admin)
        Route::get('/reports/franchisees.pdf',        [ReportController::class, 'franchiseesPdf']);
        Route::get('/reports/franchisees/{id}.pdf',   [ReportController::class, 'franchiseePdf']);
    });

/*
|--------------------------------------------------------------------------
| Routes CLIENT (guard "customer") -> Modèle Customer
|--------------------------------------------------------------------------
| Espace client authentifié (hors admin).
*/

Route::middleware(['auth:customer'])->group(function () {

    // Compte client (CRUD limité sur soi)
    Route::apiResource('customers', CustomerController::class)->only(['show', 'update', 'destroy']);

    // Commandes du client
    Route::apiResource('customer-orders', CustomerOrderController::class)->only(['index', 'store', 'show']);

    // Carte de fidélité du client
    Route::get('/my-loyalty-card', [LoyaltyCardController::class, 'show']);
    Route::put('/my-loyalty-card', [LoyaltyCardController::class, 'update']);

    // Événements du client
    Route::get('/my-events', [EventRegistrationController::class, 'myEvents']);
    Route::post('/events/{event}/register',     [EventRegistrationController::class, 'register']);
    Route::delete('/events/{event}/unregister', [EventRegistrationController::class, 'unregister']);

    // Feedback client
    Route::apiResource('feedback', CustomerFeedbackController::class)->only(['store']);

    // Newsletter (opt-in client)
    Route::apiResource('newsletter-logs', NewsletterLogController::class)->only(['store']);
});
