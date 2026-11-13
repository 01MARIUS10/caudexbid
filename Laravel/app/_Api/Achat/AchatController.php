<?php

namespace App\_Api\Achat;

use App\Http\Controllers\Controller;
use App\Modules\Product\ViewModels\ProductDetailViewModel;
use App\Modules\Product\_ProductAchat\Actions\BuyNow\BuyNowAction;
use App\Modules\Product\_ProductAchat\Actions\BuyNow\BuyNowValidation;
use App\Modules\Product\_ProductAchat\Actions\DefineBuyNowPrice\DefineBuyNowPriceAction;
use App\Modules\Product\_ProductAchat\Actions\DefineBuyNowPrice\DefineBuyNowPriceValidation;
use App\_Api\Achat\ViewModels\ProductAchatViewModel;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\_ProductBid\Repository\ProductBidRepository;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Shared\Support\MemberDirectory;
use Illuminate\Http\Request;

class AchatController extends Controller
{
    /** Administrateur : définit le prix d'achat immédiat d'un lot. */
    public function define(Request $request, int $product, DefineBuyNowPriceValidation $validation, DefineBuyNowPriceAction $action, ListMembersAction $members): ProductAchatViewModel
    {
        $achat = $action->execute($validation->validate(['produit_id' => $product] + $request->all(), $request->user()));

        return new ProductAchatViewModel($achat, MemberDirectory::forColumns($members, [$achat], 'acheteur_id'));
    }

    /** « Décider l'achat immédiat » après confirmation : le lot passe en SOLD. */
    public function buy(Request $request, int $product, BuyNowValidation $validation, BuyNowAction $action, ProductRepository $products, ProductBidRepository $bids, ListMembersAction $members): ProductDetailViewModel
    {
        $bought = $action->execute($validation->validate(['produit_id' => $product] + $request->all(), $request->user()));
        $viewer = $request->user();

        $produit = $products->find($bought->id);

        return new ProductDetailViewModel($produit, $viewer, MemberDirectory::load($members, [$produit->adjudicataireId()]), $bids->proxyOf($bought->id, $viewer->id));
    }
}
