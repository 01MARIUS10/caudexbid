<?php

namespace App\_Backoffice\Plant;

use App\Http\Controllers\Controller;
use App\Modules\Product\_ProductPlant\Actions\CreatePlant\CreatePlantAction;
use App\Modules\Product\_ProductPlant\Actions\CreatePlant\CreatePlantValidation;
use App\Modules\Product\_ProductPlant\Actions\SetPlantImage\SetPlantImageAction;
use App\Modules\Product\_ProductPlant\Actions\SetPlantImage\SetPlantImageValidation;
use App\Modules\Product\_ProductPlant\Repository\ProductPlantRepository;
use App\Modules\Product\_ProductPlant\ViewModels\BackOffice\ProductPlantBoViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Collection des plantes : information générale, réutilisée pour pré-remplir les produits (« Lots en vente »).
 */
class PlantController extends Controller
{
    public function index(Request $request, ProductPlantRepository $plants): View
    {
        return view('plant::index', [
            'plants' => ProductPlantBoViewModel::collection($plants->search($request->query('q'), $request->query('genre'))),
            'genres' => $plants->genres(),
        ]);
    }

    public function store(Request $request, CreatePlantValidation $validation, CreatePlantAction $action): RedirectResponse
    {
        $plant = $action->execute($validation->validate($request->except('_token'), $request->user()));

        return redirect()->route('bo.plants.index')->with('status', "Plante « {$plant->nom} » ajoutée à la collection.");
    }

    /** Ajoute ou remplace l'image d'une plante. */
    public function image(Request $request, int $plant, SetPlantImageValidation $validation, SetPlantImageAction $action): RedirectResponse
    {
        $model = $action->execute($validation->validate(['plante_id' => $plant, 'image' => $request->file('image')], $request->user()));

        return back()->with('status', "Image de « {$model->nom} » mise à jour.");
    }
}
