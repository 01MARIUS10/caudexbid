<?php

namespace App\_Api\Bid\ViewModels;

use App\Modules\Product\_ProductBid\Models\ProductBid;
use App\Shared\Support\MemberDirectory;
use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\Product\_ProductBid\Models\ProductBid */
class ProductBidViewModel extends ViewModel
{
    public function __construct(ProductBid $resource, protected readonly MemberDirectory $members)
    {
        parent::__construct($resource);
    }

    protected function present(): array
    {
        return [
            'id' => $this->id,
            'membre' => $this->members->identifiant($this->membre_id),
            'montant' => $this->montant,
            'pas_enchere' => $this->pas_enchere,
            'origine' => $this->origine->value,
            'a_prolonge' => $this->a_prolonge,
            'created_at' => $this->created_at,
        ];
    }
}
