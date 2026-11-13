<?php

namespace App\_Backoffice\Product\ViewModels;

use App\Modules\Product\_ProductBid\Models\ProductProxyPlafondBid;
use App\Shared\Support\MemberDirectory;
use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\Product\_ProductBid\Models\ProductProxyPlafondBid */
class ProductProxyBoViewModel extends ViewModel
{
    public function __construct(ProductProxyPlafondBid $resource, protected readonly MemberDirectory $members)
    {
        parent::__construct($resource);
    }

    protected function present(): array
    {
        return [
            'membre' => $this->members->identifiant($this->membre_id),
            'plafond' => $this->plafond,
            'est_actif' => $this->est_actif,
        ];
    }
}
