<?php

namespace App\Modules\Product\_ProductBid\Models\Enums;

enum BidSource: string
{
    /** Enchère saisie par le membre. */
    case Manual = 'manual';
    /** Enchère automatique générée par le plafond (proxy bid). */
    case Proxy = 'proxy';
}
