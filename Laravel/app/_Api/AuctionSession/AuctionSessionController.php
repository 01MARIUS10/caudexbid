<?php

namespace App\_Api\AuctionSession;

use App\Http\Controllers\Controller;
use App\Modules\Product\Actions\CreateAuctionSession\CreateAuctionSessionAction;
use App\Modules\Product\Actions\CreateAuctionSession\CreateAuctionSessionValidation;
use App\_Api\AuctionSession\ViewModels\ProductAuctionSessionViewModel;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Shared\ViewModels\ViewModelCollection;

class AuctionSessionController extends Controller
{
    /** Bandeau défilant : date de clôture / temps restant / date de la prochaine enchère. */
    public function ticker(ProductAuctionSessionRepository $sessions): JsonResponse
    {
        $current = $sessions->current();
        $next = $sessions->next($current?->cloture_le ?? now());
        $tz = config('caudexbid.display_timezone');

        return response()->json([
            'cloture_le_kst' => $current?->cloture_le->copy()->tz($tz)->format('Y-m-d H:i'),
            'temps_restant_secondes' => $current ? max(0, (int) now()->diffInSeconds($current->cloture_le, false)) : null,
            'prochaine_enchere_kst' => $next?->debut_le->copy()->tz($tz)->format('Y-m-d H:i'),
        ]);
    }

    public function index(ProductAuctionSessionRepository $sessions): ViewModelCollection
    {
        return ProductAuctionSessionViewModel::collection($sessions->all());
    }

    public function store(Request $request, CreateAuctionSessionValidation $validation, CreateAuctionSessionAction $action): ProductAuctionSessionViewModel
    {
        $session = $action->execute($validation->validate($request->all(), $request->user()));

        return (new ProductAuctionSessionViewModel($session))->created();
    }
}
