<?php

namespace App\_Api\AuctionSession\ViewModels;

use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\Product\Models\ProductAuctionSession */
class ProductAuctionSessionViewModel extends ViewModel
{
    protected function present(): array
    {
        $tz = config('caudexbid.display_timezone');

        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'debut_le' => $this->debut_le->toIso8601String(),
            'cloture_le' => $this->cloture_le->toIso8601String(),
            'cloture_le_kst' => $this->cloture_le->copy()->tz($tz)->format('Y-m-d H:i'),
            'statut' => $this->statut->value,
        ];
    }
}
