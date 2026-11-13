<?php

namespace App\Modules\Product\_ProductBid\Models;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Membre (module User) : clé étrangère membre_id ; attaché par le controller (« membre ») via MemberAttacher.
 *
 * Plafond confidentiel d'un membre sur un lot (enchères automatiques par procuration).
 *
 * @property int $membre_id
 * @property string $plafond
 * @property bool $est_actif
 */
class ProductProxyPlafondBid extends Model
{
    protected $table = 'product_proxy_plafond_bids';

    protected $fillable = ['produit_id', 'membre_id', 'plafond', 'est_actif'];

    protected function casts(): array
    {
        return [
            'plafond' => 'decimal:2',
            'est_actif' => 'boolean',
        ];
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'produit_id');
    }
}
