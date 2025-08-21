<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful; // ✅ AJOUT

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ✅ Sanctum SPA: traite les requêtes du front (5173) comme "stateful"
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        // Redirection par défaut des invités
        $middleware->redirectGuestsTo(function ($request) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated'], 401)
                : redirect()->guest('/login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
