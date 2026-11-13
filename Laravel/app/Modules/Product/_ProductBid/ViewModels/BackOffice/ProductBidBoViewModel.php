<?php

namespace App\Modules\Product\_ProductBid\ViewModels\BackOffice;

use App\Modules\Product\_ProductBid\Models\ProductBid;
use App\Shared\Support\MemberDirectory;
use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\Product\_ProductBid\Models\ProductBid */
class ProductBidBoViewModel extends ViewModel
{
    public function __construct(ProductBid $resource, protected readonly MemberDirectory $members)
    {
        parent::__construct($resource);
    }

    protected function present(): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'produit_id' => $this->produit_id,
            'numero_lot' => $this->relationLoaded('produit') ? $this->produit?->numero_lot : null,
            'plante' => $this->relationLoaded('produit') ? $this->produit?->nom : null,
            'membre_id' => $this->membre_id,
            'membre' => $this->members->identifiant($this->membre_id),
            'origine' => $this->origine->value,
            'montant' => $this->montant,
            'a_prolonge' => $this->a_prolonge,
        ];
    }
}
