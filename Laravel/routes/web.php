<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Les trois points d'entrée : un dossier de routes chacun
|--------------------------------------------------------------------------
| _Site        site public + connexion commune (/login, /logout)
| _Backoffice  back office (/backoffice, noms « bo. »)
| _Api         API JSON (/api) : middleware « api » (sans session ni CSRF) à la place du groupe « web »
*/
Route::name('site.')->group(app_path('_Site/route.php'));
Route::prefix('backoffice')->name('bo.')->group(app_path('_Backoffice/route.php'));
Route::prefix('api')->name('api.')->middleware('api')->withoutMiddleware('web')->group(app_path('_Api/route.php'));

/*
|--------------------------------------------------------------------------
| Documentation de l'API (Swagger UI) — /docs
|--------------------------------------------------------------------------
| Spécification : resources/openapi/openapi.yaml (à mettre à jour avec les routes API).
| Désactivable avec API_DOCS_ENABLED=false.
*/

if (config('caudexbid.api_docs')) {
    Route::prefix('docs')->name('docs.')->group(function () {
        Route::get('/', fn () => view('docs.swagger'))->name('ui');
        Route::get('openapi.yaml', fn () => response()->file(
            resource_path('openapi/openapi.yaml'),
            ['Content-Type' => 'application/yaml; charset=utf-8'],
        ))->name('spec');
    });
}
