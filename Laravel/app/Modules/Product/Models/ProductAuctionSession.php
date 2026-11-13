<?php

namespace App\Modules\Product\Models;

use App\Modules\Product\Models\Enums\AuctionSessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Session d'enchères mensuelle.
 *
 * @property \Illuminate\Support\Carbon $debut_le
 * @property \Illuminate\Support\Carbon $cloture_le
 */
class ProductAuctionSession extends Model
{
    protected $table = 'product_auction_sessions';

    protected $fillable = ['titre', 'debut_le', 'cloture_le', 'statut'];

    protected function casts(): array
    {
        return [
            'debut_le' => 'datetime',
            'cloture_le' => 'datetime',
            'statut' => AuctionSessionStatus::class,
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'session_enchere_id');
    }
}
