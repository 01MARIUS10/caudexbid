<?php

namespace App\_Backoffice\Media;

use App\Http\Controllers\Controller;
use App\Modules\Product\_ProductMedia\Actions\AddProductMedia\AddProductMediaAction;
use App\Modules\Product\_ProductMedia\Actions\AddProductMedia\AddProductMediaValidation;
use App\Modules\Product\_ProductMedia\Actions\DeleteProductMedia\DeleteProductMediaAction;
use App\Modules\Product\_ProductMedia\Actions\DeleteProductMedia\DeleteProductMediaValidation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function store(Request $request, int $product, AddProductMediaValidation $validation, AddProductMediaAction $action): RedirectResponse
    {
        $data = ['produit_id' => $product, 'est_vitrine' => $request->boolean('est_vitrine')]
            + array_filter($request->except('_token'), fn ($v) => $v !== null && $v !== '');

        // Un seul formulaire pour photo et vidéo : on retire les champs propres à l'autre type.
        if (($data['type'] ?? null) === 'video') {
            unset($data['position'], $data['vue'], $data['est_vitrine']);
        } else {
            unset($data['duree_secondes']);
        }

        $action->execute($validation->validate($data, $request->user()));

        return back()->with('status', 'Média enregistré.');
    }

    public function destroy(Request $request, int $media, DeleteProductMediaValidation $validation, DeleteProductMediaAction $action): RedirectResponse
    {
        $action->execute($validation->validate(['media_id' => $media], $request->user()));

        return back()->with('status', 'Média supprimé.');
    }
}
