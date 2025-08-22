<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

// Controllers Auth
use App\Http\Controllers\Api\AuthController;

// Controllers Business
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
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Routes de test et debug
|--------------------------------------------------------------------------
*/
if (config('debug.enabled')) {
    Route::get('/test', fn () => response()->json(['message' => 'API works']));
}

/*
|--------------------------------------------------------------------------
| Routes publiques (sans authentification)
|--------------------------------------------------------------------------
*/

// Consultation publique des menus, plats, événements
Route::apiResource('menus', MenuController::class)->only(['index', 'show']);
Route::apiResource('dishes', DishController::class)->only(['index', 'show']);
Route::apiResource('events', EventController::class)->only(['index']);

// Inscription client (compte Customer)
Route::post('/customers', [CustomerController::class, 'store']);

// Formulaire de contact
Route::post('/contact', [ContactController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Authentification SPA (Sanctum) - comptes en table `users`
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {

    // Public : session/cookies via middleware web
    Route::middleware('web')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login',    [AuthController::class, 'login']);

        // Renvoyer l’email de vérification (limité)
        Route::post('/email/resend', function (Request $request) {
            $request->validate([
                'email' => ['required', 'email', 'exists:users,email'],
            ]);

            // Rate limiting : 3 tentatives / 60s par IP+email
            $key = 'resend:' . sha1($request->ip() . $request->input('email'));
            if (RateLimiter::tooManyAttempts($key, 3)) {
                throw new ThrottleRequestsException('Trop de tentatives. Réessayez plus tard.');
            }
            RateLimiter::hit($key, 60);

            $user = \App\Models\User::whereEmail($request->input('email'))->first();
            if ($user && ! $user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }

            return response()->json(['message' => 'Si un compte existe, un email de vérification a été renvoyé.']);
        })->middleware('guest');
    });

    // Protégé : authentifié (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);

        // Décommente si tu as bien une méthode admin() et une Gate/Policy 'admin-only'
        // Route::get('/admin',   [AuthController::class, 'admin'])->middleware('can:admin-only');
    });
});

/*
|--------------------------------------------------------------------------
| Alias "customer" (facultatif) — réutilise AuthController
| On conserve une URL dédiée tout en gardant le même guard `web/users`.
|--------------------------------------------------------------------------
*/
Route::prefix('customer')->group(function () {

    // Public : login client via session/cookies
    Route::middleware('web')->post('/login', [AuthController::class, 'login']);

    // Espace client connecté : Sanctum + rôle 'client'
    Route::middleware(['auth:sanctum', 'role:client'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', fn (Request $request) => $request->user());
    });
});

/*
|--------------------------------------------------------------------------
| Paiements (protégé Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders/{order}/payment-intent', [PaymentController::class, 'paymentIntent']);
    Route::post('/orders/{order}/confirm-payment', [PaymentController::class, 'confirmPayment']);
});

/*
|--------------------------------------------------------------------------
| Administration (Sanctum + activation + rôle admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'activated', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', fn () => response()->json(['message' => 'Bienvenue Admin']));

        // Gestion franchisés
        Route::apiResource('franchisees', FranchiseeController::class);
        Route::get('/franchisees/{id}/pdf', [FranchiseeController::class, 'generatePdf']);

        // Camions & maintenances
        Route::apiResource('trucks', TruckController::class);
        Route::apiResource('truck-maintenances', TruckMaintenanceController::class);

        // Stock
        Route::apiResource('warehouses', WarehouseController::class);
        Route::apiResource('stock-items', StockItemController::class);
        Route::apiResource('stock-orders', StockOrderController::class);
        Route::apiResource('stock-order-items', StockOrderItemController::class);

        // Ventes
        Route::apiResource('sales', SaleController::class);
        Route::get('/sales/pdf', [SaleController::class, 'generatePdf']);

        // Utilisateurs
        Route::get('/users', [AdminController::class, 'index']);
        Route::patch('/users/{user}/activate',   [AdminController::class, 'activate']);
        Route::patch('/users/{user}/suspend',    [AdminController::class, 'suspend']);
        Route::patch('/users/{user}/make-admin', [AdminController::class, 'makeAdmin']);

        // Menus & plats (admin)
        Route::apiResource('menus', MenuController::class)->except(['index', 'show']);
        Route::apiResource('dishes', DishController::class)->except(['index', 'show']);

        // Commandes (admin)
        Route::apiResource('orders', OrderController::class);
        Route::apiResource('order-items', OrderItemController::class);

        // Cartes de fidélité
        Route::apiResource('loyalty-cards', LoyaltyCardController::class);

        // Newsletter (admin)
        Route::apiResource('newsletter-logs', NewsletterLogController::class)->only(['index']);
        Route::post('/newsletters/send', [NewsletterController::class, 'send']);

        // Événements (admin)
        Route::apiResource('events', EventController::class)->except(['index']);
        Route::get('/events/{event}/participants', [EventRegistrationController::class, 'eventParticipants']);

        // Clients (consultation)
        Route::apiResource('customers', CustomerController::class)->only(['index']);

        // Rapports PDF
        Route::get('/reports/franchisees.pdf', [ReportController::class, 'franchiseesPdf']);
        Route::get('/reports/franchisees/{id}.pdf', [ReportController::class, 'franchiseePdf']);
    });

/*
|--------------------------------------------------------------------------
| Espace Client — routes applicatives (Sanctum + rôle client)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:client'])->group(function () {

    // Profil client
    Route::apiResource('customers', CustomerController::class)->only(['show', 'update', 'destroy']);

    // Commandes du client
    Route::apiResource('customer-orders', CustomerOrderController::class)->only(['index', 'store', 'show']);

    // Carte de fidélité
    Route::get('/my-loyalty-card', [LoyaltyCardController::class, 'show']);
    Route::put('/my-loyalty-card', [LoyaltyCardController::class, 'update']);

    // Événements client
    Route::get('/my-events', [EventRegistrationController::class, 'myEvents']);
    Route::post('/events/{event}/register', [EventRegistrationController::class, 'register']);
    Route::delete('/events/{event}/unregister', [EventRegistrationController::class, 'unregister']);

    // Feedback
    Route::apiResource('feedback', CustomerFeedbackController::class)->only(['store']);

    // Newsletter (opt-in client)
    Route::apiResource('newsletter-logs', NewsletterLogController::class)->only(['store']);
});
