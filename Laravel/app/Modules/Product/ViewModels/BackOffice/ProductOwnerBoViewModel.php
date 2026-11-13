<?php

namespace App\Modules\Product\ViewModels\BackOffice;

use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\Product\Models\ProductOwner */
class ProductOwnerBoViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'pays' => $this->pays,
            'email' => $this->email,
            'infos_bancaires' => $this->infos_bancaires,
            'products_count' => $this->products_count,
        ];
    }
}
