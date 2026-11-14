<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Langue du site public : anglais, français, japonais, chinois, coréen (anglais par défaut), choisie via
 * /lang/{locale} et mémorisée en session.
 *
 * Alias « locale » enregistré automatiquement par ModuleServiceProvider (constante ALIAS).
 */
class SetLocale
{
    public const ALIAS = 'locale';

    public const LOCALES = ['en' => 'English', 'fr' => 'Français', 'ja' => '日本語', 'zh' => '中文', 'ko' => '한국어'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        app()->setLocale(array_key_exists($locale, self::LOCALES) ? $locale : 'en');

        return $next($request);
    }
}
