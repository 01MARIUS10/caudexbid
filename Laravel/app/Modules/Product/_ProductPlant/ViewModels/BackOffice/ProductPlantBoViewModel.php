<?php

namespace App\Modules\Product\_ProductPlant\ViewModels\BackOffice;

use App\Shared\ViewModels\ViewModel;

/**
 * Plante de la collection (liste du back office, valeurs par défaut du formulaire produit).
 *
 * @mixin \App\Modules\Product\_ProductPlant\Models\ProductPlant
 */
class ProductPlantBoViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'genre' => $this->genre,
            'image_url' => $this->imageUrl(),
            'products_count' => $this->when(isset($this->products_count), fn () => $this->products_count),
        ];
    }
}
