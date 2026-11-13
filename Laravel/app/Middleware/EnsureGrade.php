<?php

namespace App\Middleware;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restreint une route à certains grades (voir GradeCode : G, M, P, A).
 *
 * Alias « grade » enregistré automatiquement par ModuleServiceProvider (constante ALIAS).
 * Dans les routes, utiliser EnsureGrade::for() plutôt qu'une chaîne :
 *
 *     Route::middleware(EnsureGrade::for(GradeCode::Admin))                       // = 'grade:A'
 *     Route::middleware(EnsureGrade::for(GradeCode::Regulier, GradeCode::Premium)) // = 'grade:M,P'
 */
class EnsureGrade
{
    public const ALIAS = 'grade';

    /** Construit la déclaration de middleware pour les grades donnés, ex. 'grade:M,P'. */
    public static function for(GradeCode ...$codes): string
    {
        return self::ALIAS.':'.implode(',', array_map(fn (GradeCode $c) => $c->value, $codes));
    }

    public function handle(Request $request, Closure $next, string ...$codes): Response
    {
        /** @var UserAuth|null $user */
        $user = $request->user();
        $allowed = array_map(fn (string $c) => GradeCode::from($c), $codes);

        if (! $user || ! $user->hasGrade(...$allowed)) {
            $message = 'Action non autorisée pour votre grade.';

            return $request->expectsJson() || $request->is('api/*')
                ? response()->json(['message' => $message], 403)
                : abort(403, $message);
        }

        return $next($request);
    }
}
