<?php

namespace App\_Api\ProductOwner\ViewModels;

use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\Product\Models\ProductOwner */
class ProductOwnerViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'pays' => $this->pays,
            'email' => $this->email,
        ];
    }
}
