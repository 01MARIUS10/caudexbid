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
