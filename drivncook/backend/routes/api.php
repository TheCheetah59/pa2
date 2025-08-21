<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
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

// Inscription client
Route::post('/customers', [CustomerController::class, 'store']);

// Formulaire de contact
Route::post('/contact', [ContactController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Auth API
| NB: /login, /logout, /register, /activate sont dans web.php (session Sanctum)
|--------------------------------------------------------------------------
*/


// Utilisateur admin courant (expose des infos si déjà auth Sanctum)
Route::get('/auth/admin', [AuthController::class, 'admin'])
    ->middleware(['auth:sanctum', 'can:admin-only']);

// GET /api/me -> utilisateur connecté via guard "sanctum"
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

/*
|--------------------------------------------------------------------------
| Routes ADMIN/STAFF (auth:sanctum) -> Modèle User
|--------------------------------------------------------------------------
|
| Pour éviter les collisions avec les routes publiques (menus, dishes, events),
| on met un préfixe /admin aux ressources admin.
|
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

        // Stock
        Route::apiResource('warehouses',       WarehouseController::class);
        Route::apiResource('stock-items',      StockItemController::class);
        Route::apiResource('stock-orders',     StockOrderController::class);
        Route::apiResource('stock-order-items',StockOrderItemController::class);

        // Ventes
        Route::apiResource('sales', SaleController::class);
        Route::get('/sales/pdf', [SaleController::class, 'generatePdf']);

        // Utilisateurs
        Route::get('/users', [AdminController::class, 'index']);
        Route::patch('/users/{user}/activate',   [AdminController::class, 'activate']);
        Route::patch('/users/{user}/suspend',    [AdminController::class, 'suspend']);
        Route::patch('/users/{user}/make-admin', [AdminController::class, 'makeAdmin']);

        // Menus / Plats (admin) — pas de collision avec public grâce au prefix /admin
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
    });

/*
|--------------------------------------------------------------------------
| Routes CLIENT (auth:customer) -> Modèle Customer
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:customer'])->group(function () {

    // GET /api/customer/profile -> profil client via guard "customer"
    Route::get('/customer/profile', fn (Request $request) => $request->user('customer'));


    // Compte client
    Route::apiResource('customers', CustomerController::class)->only(['show', 'update', 'destroy']);

    // Commandes du client
    Route::apiResource('customer-orders', CustomerOrderController::class)->only(['index', 'store', 'show']);

    // Carte de fidélité du client
    Route::get('/my-loyalty-card',  [LoyaltyCardController::class, 'show']);
    Route::put('/my-loyalty-card',  [LoyaltyCardController::class, 'update']);

    // Événements du client
    Route::get('/my-events', [EventRegistrationController::class, 'myEvents']);
    Route::post('/events/{event}/register',    [EventRegistrationController::class, 'register']);
    Route::delete('/events/{event}/unregister',[EventRegistrationController::class, 'unregister']);

    // Feedback client
    Route::apiResource('feedback', CustomerFeedbackController::class)->only(['store']);

    // Newsletter (opt-in client)
    Route::apiResource('newsletter-logs', NewsletterLogController::class)->only(['store']);
});
