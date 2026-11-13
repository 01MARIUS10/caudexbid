<?php

namespace App\Modules\Product\_ProductAchat\Models;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Membre (module User) : clé étrangère acheteur_id ; attaché par le controller (« acheteur ») via MemberAttacher.
 *
 * Achat immédiat d'un produit (1-1). acheteur_id null tant que personne n'a acheté.
 *
 * @property string $prix
 * @property int|null $acheteur_id
 */
class ProductAchat extends Model
{
    protected $table = 'product_achat';

    protected $fillable = ['produit_id', 'prix', 'acheteur_id', 'achete_le'];

    protected function casts(): array
    {
        return [
            'prix' => 'decimal:2',
            'achete_le' => 'datetime',
        ];
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'produit_id');
    }

    public function isBought(): bool
    {
        return $this->acheteur_id !== null;
    }
}
