<?php

namespace App\_Site\Home;

use App\Http\Controllers\Controller;
use App\Modules\Product\_ProductMedia\Repository\ProductMediaRepository;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Page d'accueil publique (visiteurs non authentifiés) : bandeau défilant (clôture / temps restant /
 * prochaine enchère) et 20 photos de présentation maximum, sans aucune information sur les plantes.
 */
class HomeController extends Controller
{
    public function __invoke(ProductMediaRepository $media, ProductAuctionSessionRepository $sessions): View
    {
        $current = $sessions->current();
        $next = $sessions->next($current?->cloture_le ?? now());
        $tz = config('caudexbid.display_timezone');
        $locale = app()->getLocale();

        return view('home::home', [
            'photos' => $media->showcase(config('caudexbid.media.showcase_max'))->map->url(),
            'ticker' => [
                'cloture' => $current?->cloture_le->copy()->tz($tz)->format('Y-m-d H:i'),
                'cloture_iso' => $current?->cloture_le->toIso8601String(),
                'restant' => $current?->cloture_le->isFuture()
                    ? $current->cloture_le->copy()->locale($locale)->diffForHumans(['parts' => 2, 'syntax' => CarbonInterface::DIFF_ABSOLUTE])
                    : null,
                'prochaine' => $next?->debut_le->copy()->tz($tz)->format('Y-m-d H:i'),
            ],
        ]);
    }

    public function locale(Request $request, string $locale): RedirectResponse
    {
        $request->session()->put('locale', $locale);

        return redirect()->route('site.home');
    }
}
