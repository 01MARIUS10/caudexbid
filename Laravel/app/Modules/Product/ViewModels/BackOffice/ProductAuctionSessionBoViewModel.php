<?php

namespace App\Modules\Product\ViewModels\BackOffice;

use App\Shared\ViewModels\ViewModel;

/**
 * Session d'enchères pour le back office (dates en Carbon, formatées en KST par la vue).
 *
 * @mixin \App\Modules\Product\Models\ProductAuctionSession
 */
class ProductAuctionSessionBoViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'statut' => $this->statut->value,
            'debut_le' => $this->debut_le,
            'cloture_le' => $this->cloture_le,
            'products_count' => $this->products_count,
        ];
    }
}
