<?php

namespace App\Modules\Product\_ProductBid\Models;

use App\Modules\Product\Models\Product;
use App\Modules\Product\_ProductBid\Models\Enums\BidSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Membre (module User) : clé étrangère membre_id ; attaché par le controller (« membre ») via MemberAttacher.
 *
 * Table « Informations d'enchère » : une ligne par enchère, jamais modifiée.
 */
class ProductBid extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'product_bids';

    protected $fillable = ['produit_id', 'membre_id', 'montant', 'pas_enchere', 'origine', 'enchere_auto_id', 'a_prolonge'];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'pas_enchere' => 'decimal:2',
            'origine' => BidSource::class,
            'a_prolonge' => 'boolean',
        ];
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'produit_id');
    }

    public function plafond(): BelongsTo
    {
        return $this->belongsTo(ProductProxyPlafondBid::class, 'enchere_auto_id');
    }
}
