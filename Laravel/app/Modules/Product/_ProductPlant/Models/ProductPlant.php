<?php

namespace App\Modules\Product\_ProductPlant\Models;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Plante de la collection : information générale (nom, genre, image).
 * Pas de propriétaire, de prix, de taille ni de description : ce sont des propriétés du produit (le spécimen mis en vente).
 *
 * Sert à pré-remplir le formulaire d'un produit, au classement et aux statistiques.
 * L'image est une identité visuelle de la collection (back office) : elle n'est jamais utilisée pour un produit.
 *
 * @property int $id
 * @property string $nom
 * @property string|null $genre
 * @property string|null $image chemin sur le disque des médias
 */
class ProductPlant extends Model
{
    protected $table = 'product_plants';

    /** Champs du formulaire produit pré-remplis quand on choisit la plante. */
    public const DEFAULT_FIELDS = ['nom'];

    protected $fillable = ['nom', 'genre', 'image'];

    /** Produits liés à cette plante (les produits réglés sont supprimés). */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'plante_id');
    }

    public function imageUrl(): ?string
    {
        return $this->image ? Storage::disk(config('caudexbid.media.disk'))->url($this->image) : null;
    }
}
