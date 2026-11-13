<?php

namespace App\_Backoffice\Bid;

use App\Modules\Product\ViewModels\BackOffice\ProductAuctionSessionBoViewModel;
use App\Modules\Product\_ProductBid\ViewModels\BackOffice\ProductBidBoViewModel;
use App\Http\Controllers\Controller;
use App\Modules\Product\_ProductBid\Repository\ProductBidRepository;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Shared\Support\MemberDirectory;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Journal de toutes les enchères (manuelles et automatiques).
 */
class BidController extends Controller
{
    public function index(Request $request, ProductBidRepository $bids, ProductAuctionSessionRepository $sessions, UserAuthRepository $users, ListMembersAction $listMembers): View
    {
        $page = $bids->search($request->integer('session') ?: null, $request->integer('membre') ?: null);
        $directory = MemberDirectory::forColumns($listMembers, $page, 'membre_id');

        return view('bid::index', [
            'bids' => ProductBidBoViewModel::collection($page, fn ($b) => new ProductBidBoViewModel($b, $directory)),
            'sessions' => ProductAuctionSessionBoViewModel::collection($sessions->all()),
            'members' => $users->options(),
        ]);
    }
}
