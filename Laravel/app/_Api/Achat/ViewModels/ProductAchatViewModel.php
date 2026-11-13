<?php

namespace App\_Api\Achat\ViewModels;

use App\Modules\Product\_ProductAchat\Models\ProductAchat;
use App\Shared\Support\MemberDirectory;
use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\Product\_ProductAchat\Models\ProductAchat */
class ProductAchatViewModel extends ViewModel
{
    public function __construct(ProductAchat $resource, protected readonly MemberDirectory $members)
    {
        parent::__construct($resource);
    }

    protected function present(): array
    {
        return [
            'produit_id' => $this->produit_id,
            'prix' => $this->prix,
            'acheteur' => $this->members->identifiant($this->acheteur_id),
            'achete_le' => $this->achete_le,
        ];
    }
}
