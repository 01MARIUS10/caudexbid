<?php

namespace App\_Site\Home;

use App\_Site\Home\ViewModels\HomeProductViewModel;
use App\Http\Controllers\Controller;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\View\View;

/**
 * Page d'accueil publique (visiteurs non authentifiés) : tous les produits publiés,
 * sans aucune information de prix ni de vente.
 */
class HomeController extends Controller
{
    public function __invoke(ProductRepository $products): View
    {
        return view('home::home', ['products' => HomeProductViewModel::collection($products->published())]);
    }
}
