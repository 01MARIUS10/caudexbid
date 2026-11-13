<?php

namespace App\_Api\Bid;

use App\Http\Controllers\Controller;
use App\Modules\Product\ViewModels\ProductDetailViewModel;
use App\Modules\Product\_ProductBid\Actions\PlaceBid\PlaceBidAction;
use App\Modules\Product\_ProductBid\Actions\PlaceBid\PlaceBidValidation;
use App\_Api\Bid\ViewModels\ProductBidViewModel;
use App\Modules\Product\_ProductBid\Repository\ProductBidRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Shared\Support\MemberDirectory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Shared\ViewModels\ViewModelCollection;

class BidController extends Controller
{
    /** Bouton « Confirmer » de l'écran d'enchère. */
    public function store(
        Request $request,
        int $product,
        PlaceBidValidation $validation,
        PlaceBidAction $action,
        ProductRepository $products,
        ProductBidRepository $bids,
        ListMembersAction $members,
    ): JsonResponse {
        $result = $action->execute($validation->validate(['produit_id' => $product] + $request->all(), $request->user()));

        $produit = $products->find($product);
        $directory = MemberDirectory::load($members, [$request->user()->id, $result['bid']?->membre_id, $produit->adjudicataireId()]);

        return response()->json([
            'enchere' => $result['bid'] ? new ProductBidViewModel($result['bid'], $directory) : null,
            'est_meilleur_encherisseur' => $result['est_meilleur_encherisseur'],
            'prolongation' => $result['prolongation'],
            'produit' => new ProductDetailViewModel($produit, $request->user(), $directory, $bids->proxyOf($product, $request->user()->id)),
        ], 201);
    }

    /** Historique complet des enchères d'un lot (administrateur). */
    public function history(int $product, ProductBidRepository $bids, ListMembersAction $members): ViewModelCollection
    {
        $history = $bids->history($product);
        $directory = MemberDirectory::forColumns($members, $history, 'membre_id');

        return ProductBidViewModel::collection($history, fn ($bid) => new ProductBidViewModel($bid, $directory));
    }
}
