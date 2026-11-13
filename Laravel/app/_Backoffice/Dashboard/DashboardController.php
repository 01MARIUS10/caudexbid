<?php

namespace App\_Backoffice\Dashboard;

use App\Modules\Product\ViewModels\BackOffice\ProductBoViewModel;
use App\Modules\Product\ViewModels\BackOffice\ProductAuctionSessionBoViewModel;
use App\Http\Controllers\Controller;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Shared\Support\MemberDirectory;
use App\Modules\Message\Repository\MessageRepository;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Tableau de bord du back office : vue d'ensemble de tous les modules.
 */
class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        UserAuthRepository $users,
        ProductRepository $products,
        ProductAuctionSessionRepository $sessions,
        MessageRepository $messages,
        ListMembersAction $members,
    ): View {
        $current = $sessions->current();
        $unsettled = $products->unsettled();

        return view('dashboard::index', [
            'membresParGrade' => $users->countByGrade(),
            'lotsParStatut' => $products->countByStatus(),
            'session' => $current ? ProductAuctionSessionBoViewModel::make($current) : null,
            'prochaine' => ($next = $sessions->next($current?->cloture_le ?? now())) ? ProductAuctionSessionBoViewModel::make($next) : null,
            'nonSoldes' => ProductBoViewModel::collection($unsettled, $this->product(MemberDirectory::load($members, $unsettled->flatMap(fn ($p) => [$p->auteur_prix_actuel_id, $p->achat?->acheteur_id])))),
            'messagesNonLus' => $messages->unreadCountFor($request->user()->id),
        ]);
    }

    private function product(MemberDirectory $directory): \Closure
    {
        return fn ($p) => new ProductBoViewModel($p, $directory);
    }
}
