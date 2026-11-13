<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Propriétaire des plantes / pépinière.
 */
class ProductOwner extends Model
{
    protected $table = 'product_owners';

    protected $fillable = ['nom', 'pays', 'email', 'infos_bancaires'];

    protected $hidden = ['infos_bancaires'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'proprietaire_id');
    }
}
