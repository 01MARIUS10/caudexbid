<?php

namespace App\_Backoffice\Achat;

use App\Http\Controllers\Controller;
use App\Modules\Product\_ProductAchat\Actions\DefineBuyNowPrice\DefineBuyNowPriceAction;
use App\Modules\Product\_ProductAchat\Actions\DefineBuyNowPrice\DefineBuyNowPriceValidation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AchatController extends Controller
{
    public function define(Request $request, int $product, DefineBuyNowPriceValidation $validation, DefineBuyNowPriceAction $action): RedirectResponse
    {
        $action->execute($validation->validate(['produit_id' => $product, 'prix' => $request->input('prix')], $request->user()));

        return back()->with('status', 'Prix d\'achat immédiat enregistré.');
    }
}
