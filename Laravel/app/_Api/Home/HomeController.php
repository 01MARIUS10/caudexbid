<?php

namespace App\_Api\Home;

use App\Http\Controllers\Controller;
use App\Modules\Product\_ProductMedia\Repository\ProductMediaRepository;
use Illuminate\Http\JsonResponse;

/**
 * Écran initial (non connecté) : grille de 20 photos max, sans information sur la plante.
 */
class HomeController extends Controller
{
    public function __invoke(ProductMediaRepository $media): JsonResponse
    {
        return response()->json([
            'photos' => $media->showcase(config('caudexbid.media.showcase_max'))->map->url()->values(),
            'liens' => [
                'politique_de_fonctionnement' => url('/politique'),
                'informations_exploitant' => [
                    // Pas de numéro de téléphone : nom du site et e-mail uniquement.
                    'site' => config('caudexbid.exploitant.site'),
                    'email' => config('caudexbid.exploitant.email'),
                ],
            ],
        ]);
    }
}
