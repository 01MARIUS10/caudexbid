<?php

namespace App\Modules\Product\_ProductMedia\ViewModels;

use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\Product\_ProductMedia\Models\ProductMedia */
class ProductMediaViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'position' => $this->position,
            'vue' => $this->vue?->value,
            'url' => $this->url(),
            'duree_secondes' => $this->duree_secondes,
            'est_vitrine' => $this->est_vitrine,
        ];
    }
}
