<?php

namespace App\_Site\Home\ViewModels;

use App\Shared\ViewModels\ViewModel;

/**
 * Lot sur la page d'accueil publique : aucune information de prix ni de vente.
 *
 * @mixin \App\Modules\Product\Models\Product
 */
class HomeProductViewModel extends ViewModel
{
    protected function present(): array
    {
        $photos = $this->media->filter(fn ($m) => $m->type->value === 'photo');

        return [
            'numero_lot' => $this->numero_lot,
            'nom' => $this->nom,
            'description_1' => $this->description_1,
            'photo' => ($photos->firstWhere('position', 1) ?? $photos->first())?->url(),
        ];
    }
}
