<?php

namespace App\Modules\Product\Models\Enums;

enum AuctionSessionStatus: string
{
    case Scheduled = 'scheduled';
    case Open = 'open';
    case Closed = 'closed';
    case Settled = 'settled';
}
