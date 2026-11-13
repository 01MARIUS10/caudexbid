<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 * Enregistre :
 * - les vues de chaque page : app/_Site/{Page}/Resources/views et app/_Backoffice/{Page}/Resources/views
 *   (namespace = nom du dossier en kebab-case : _Backoffice/Product => product::index) ;
 * - les middlewares (app/Middleware/*.php) qui déclarent une constante ALIAS
 *   (ex. Middleware/EnsureGrade => « grade »).
 *
 * Les routes sont regroupées par dossier (app/_Api/route.php, app/_Backoffice/route.php, app/_Site/route.php),
 * appelés par routes/api.php et routes/web.php.
 */
class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Un dossier de vues par route : _Site/{Page}, _Backoffice/{Page} (namespace = nom du dossier en kebab-case).
        $dirs = array_merge(
            glob(app_path('_Site/*/Resources/views'), GLOB_ONLYDIR) ?: [],
            glob(app_path('_Backoffice/*/Resources/views'), GLOB_ONLYDIR) ?: [],
        );

        foreach ($dirs as $dir) {
            $this->loadViewsFrom($dir, Str::kebab(basename(dirname($dir, 2))));
        }

        $this->registerMiddlewareAliases($this->app->make(Router::class));
    }

    private function registerMiddlewareAliases(Router $router): void
    {
        $files = array_merge(
            glob(app_path('Middleware/*.php')) ?: [],
        );

        foreach ($files as $file) {
            $class = 'App\\'.str_replace(['/', '.php'], ['\\', ''], substr($file, strlen(app_path()) + 1));
            if (class_exists($class) && defined("{$class}::ALIAS")) {
                $router->aliasMiddleware($class::ALIAS, $class);
            }
        }
    }
}
