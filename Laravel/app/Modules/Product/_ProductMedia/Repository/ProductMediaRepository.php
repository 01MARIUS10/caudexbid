<?php

namespace App\Modules\Product\_ProductMedia\Repository;

use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\_ProductMedia\Models\Enums\MediaType;
use App\Modules\Product\_ProductMedia\Models\ProductMedia;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ProductMediaRepository
{
    public function find(int $id): ?ProductMedia
    {
        return ProductMedia::find($id);
    }

    public function findSlot(int $produitId, MediaType $type, int $position): ?ProductMedia
    {
        return ProductMedia::where('produit_id', $produitId)
            ->where('type', $type)
            ->where('position', $position)
            ->first();
    }

    /**
     * Photo d'un produit lié à la plante, pour compléter l'image de la plante (commande plants:fill-images) :
     * photo 1 (face) en priorité, sinon la première photo ; produit le plus récent d'abord.
     */
    public function firstPhotoOfPlant(int $planteId): ?ProductMedia
    {
        return ProductMedia::query()
            ->select('product_media.*')
            ->join('products', 'products.id', '=', 'product_media.produit_id')
            ->where('products.plante_id', $planteId)
            ->where('product_media.type', MediaType::Photo)
            ->orderBy('product_media.position')
            ->orderByDesc('products.id')
            ->first();
    }

    /**
     * Copie un média (fichier compris) vers un autre produit : le nouveau lot a ses propres fichiers,
     * indépendants de ceux de l'ancien lot (supprimés à son règlement). Jamais en vitrine par défaut.
     */
    public function copyTo(ProductMedia $source, int $produitId): ?ProductMedia
    {
        $disk = Storage::disk(config('caudexbid.media.disk'));
        if (! $disk->exists($source->chemin)) {
            return null;
        }

        $target = 'products/'.$produitId.'/'.\Illuminate\Support\Str::random(40).'.'.pathinfo($source->chemin, PATHINFO_EXTENSION);
        $disk->copy($source->chemin, $target);

        return $this->create([
            'produit_id' => $produitId,
            'type' => $source->type,
            'position' => $source->position,
            'vue' => $source->vue,
            'chemin' => $target,
            'duree_secondes' => $source->duree_secondes,
            'est_vitrine' => false,
        ]);
    }

    public function create(array $attributes): ProductMedia
    {
        return ProductMedia::create($attributes);
    }

    /** Supprime la ligne et le fichier stocké. */
    public function delete(ProductMedia $media): void
    {
        Storage::disk(config('caudexbid.media.disk'))->delete($media->chemin);
        $media->delete();
    }

    /** Supprime les fichiers de tous les médias d'un produit (les lignes partent en cascade). */
    public function deleteFilesOf(int $produitId): void
    {
        $paths = ProductMedia::where('produit_id', $produitId)->pluck('chemin')->all();
        Storage::disk(config('caudexbid.media.disk'))->delete($paths);
    }

    public function countShowcase(?int $exceptId = null): int
    {
        return ProductMedia::where('est_vitrine', true)
            ->when($exceptId, fn ($q) => $q->whereKeyNot($exceptId))
            ->count();
    }

    /** Écran initial : 20 photos représentatives maximum, sans information sur la plante. */
    public function showcase(int $limit): Collection
    {
        return ProductMedia::where('type', MediaType::Photo)
            ->where('est_vitrine', true)
            ->whereHas('produit', fn ($q) => $q->where('statut', '!=', ProductStatus::Draft))
            ->latest('id')
            ->limit($limit)
            ->get();
    }
}
