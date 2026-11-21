<?php

namespace App\Providers;

use App\Modules\User\_UserAuth\ViewModels\BackOffice\MemberBoViewModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Layout du back office : le membre connecté est fourni sous forme de ViewModel.
        View::composer('components.bo.layout', fn ($view) => $view->with('authUser', Auth::user() ? MemberBoViewModel::make(Auth::user()->loadMissing('currentUserGrade.grade', 'profile.parrain', 'preference'))->toArray() : null));

        // @kst($date) : affiche une date en heure coréenne (fuseau d'affichage du site).
        Blade::directive('kst', fn (string $expr) => "<?php echo e(($expr)?->copy()->tz(config('caudexbid.display_timezone'))->format('Y-m-d H:i') ?? '—'); ?>");

        // @money($montant) : prix formaté en dollars (« 1 250 $ »), tiret si vide. Voir Money::format().
        Blade::directive('money', fn (string $expr) => "<?php echo e(\\App\\Shared\\Support\\Money::format($expr)); ?>");
    }
}
