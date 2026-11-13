<?php

namespace App\Modules\Product\_ProductMedia\Models;

use App\Modules\Product\Models\Product;
use App\Modules\Product\_ProductMedia\Models\Enums\MediaType;
use App\Modules\Product\_ProductMedia\Models\Enums\PhotoView;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Photo (1-4) ou vidéo (1) d'un produit.
 *
 * @property string $chemin
 */
class ProductMedia extends Model
{
    protected $table = 'product_media';

    protected $fillable = ['produit_id', 'type', 'position', 'vue', 'chemin', 'duree_secondes', 'est_vitrine'];

    protected function casts(): array
    {
        return [
            'type' => MediaType::class,
            'vue' => PhotoView::class,
            'position' => 'integer',
            'duree_secondes' => 'integer',
            'est_vitrine' => 'boolean',
        ];
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'produit_id');
    }

    public function url(): string
    {
        return Storage::disk(config('caudexbid.media.disk'))->url($this->chemin);
    }
}
