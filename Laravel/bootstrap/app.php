<?php

use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // L'alias « grade » est enregistré par ModuleServiceProvider depuis app/Middleware/EnsureGrade.
        // Chaque route déclare elle-même sa combinaison de grades : EnsureGrade::for(GradeCode::...).
        $middleware->redirectGuestsTo(fn () => route('bo.login'));
        $middleware->redirectUsersTo(fn () => route('bo.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // API : JSON. Back office : retour au formulaire avec le message d'erreur.
        $exceptions->render(function (BusinessRuleException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => $e->getMessage()], $e->status);
            }

            return back()->withInput()->withErrors(['business' => $e->getMessage()]);
        });
    })->create();
