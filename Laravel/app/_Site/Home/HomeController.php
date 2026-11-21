<?php

namespace App\_Site\Home;

use App\Http\Controllers\Controller;
use App\Modules\Message\Actions\CountUnreadMessages\CountUnreadMessagesAction;
use App\Modules\Message\Actions\CountUnreadMessages\CountUnreadMessagesInput;
use App\Modules\Product\Models\ProductAuctionSession;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\ViewModels\ProductCardViewModel;
use App\Modules\Product\_ProductMedia\Repository\ProductMediaRepository;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersInput;
use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Shared\Support\MemberDirectory;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * « / » :
 * - visiteur : bandeau défilant et 20 photos de présentation maximum, sans aucune information sur les plantes ;
 * - membre connecté : menu membre + lots de la session en cours (photo 1 et résumé) + liste des membres premium.
 */
class HomeController extends Controller
{
    public function __invoke(
        Request $request,
        ProductMediaRepository $media,
        ProductAuctionSessionRepository $sessions,
        ProductRepository $products,
        ListMembersAction $members,
        CountUnreadMessagesAction $unread,
    ): View {
        $current = $sessions->current();
        $ticker = $this->ticker($current, $sessions->next($current?->cloture_le ?? now()));

        /** @var UserAuth|null $user */
        $user = $request->user();

        if (! $user) {
            return view('home::home', [
                'photos' => $media->showcase(config('caudexbid.media.showcase_max'))->map->url(),
                'ticker' => $ticker,
            ]);
        }

        $items = $current ? $products->forSession($current->id) : collect();
        $directory = MemberDirectory::load($members, $items->map->adjudicataireId());

        return view('home::member', [
            'ticker' => $ticker,
            'user' => [
                'identifiant' => $user->identifiant,
                'grade' => $user->gradeCode()->value,
                'is_admin' => $user->isAdmin(),
                // Changement de grade (G <-> M) des filleuls : réservé aux grades P et A.
                'can_change_grade' => $user->hasGrade(GradeCode::Premium, GradeCode::Admin),
                'unread' => $unread->execute(new CountUnreadMessagesInput($user->id))->count,
            ],
            'session' => $current?->titre,
            'products' => ProductCardViewModel::collection($items, fn ($p) => new ProductCardViewModel($p, $user, $directory))->toArray(),
            // Destinataires de « Envoyer un message » (demande de membre régulier, inscription…).
            'premiums' => $members->execute(new ListMembersInput(grades: [GradeCode::Premium]))
                ->reject(fn (UserAuth $m) => $m->id === $user->id)
                ->map(fn (UserAuth $m) => ['id' => $m->id, 'identifiant' => $m->identifiant])
                ->values(),
        ]);
    }

    public function locale(Request $request, string $locale): RedirectResponse
    {
        $request->session()->put('locale', $locale);

        // Retour à la page d'origine (site ou back office), l'accueil à défaut.
        return redirect()->back(fallback: route('site.home'));
    }

    /** Bandeau défilant : date de clôture / temps restant / date de la prochaine enchère. */
    private function ticker(?ProductAuctionSession $current, ?ProductAuctionSession $next): array
    {
        $tz = config('caudexbid.display_timezone');
        $locale = app()->getLocale();

        return [
            'cloture' => $current?->cloture_le->copy()->tz($tz)->format('Y-m-d H:i'),
            'cloture_iso' => $current?->cloture_le->toIso8601String(),
            'restant' => $current?->cloture_le->isFuture()
                ? $current->cloture_le->copy()->locale($locale)->diffForHumans(['parts' => 2, 'syntax' => CarbonInterface::DIFF_ABSOLUTE])
                : null,
            'prochaine' => $next?->debut_le->copy()->tz($tz)->format('Y-m-d H:i'),
        ];
    }
}
