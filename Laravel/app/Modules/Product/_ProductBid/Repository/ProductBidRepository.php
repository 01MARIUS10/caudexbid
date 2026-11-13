<?php

namespace App\Modules\Product\_ProductBid\Repository;

use App\Modules\Product\_ProductBid\Models\ProductBid;
use App\Modules\Product\_ProductBid\Models\ProductProxyPlafondBid;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductBidRepository
{
    public function create(array $attributes): ProductBid
    {
        return ProductBid::create($attributes);
    }

    public function markExtension(ProductBid $bid): void
    {
        $bid->forceFill(['a_prolonge' => true])->save();
    }

    public function history(int $produitId): Collection
    {
        return ProductBid::where('produit_id', $produitId)
            ->orderByDesc('id')
            ->get();
    }

    /** Toutes les enchères (back office), filtres : session, membre. */
    public function search(?int $sessionId = null, ?int $membreId = null, int $perPage = 50): LengthAwarePaginator
    {
        return ProductBid::with('produit')
            ->when($sessionId, fn ($q) => $q->whereHas('produit', fn ($p) => $p->where('session_enchere_id', $sessionId)))
            ->when($membreId, fn ($q) => $q->where('membre_id', $membreId))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** Plafonds (proxy) d'un lot, du plus haut au plus bas. */
    public function proxiesOf(int $produitId): Collection
    {
        return ProductProxyPlafondBid::where('produit_id', $produitId)
            ->orderByDesc('plafond')
            ->get();
    }

    public function distinctBidderCount(int $produitId): int
    {
        return ProductBid::where('produit_id', $produitId)->distinct()->count('membre_id');
    }

    public function proxyOf(int $produitId, int $membreId): ?ProductProxyPlafondBid
    {
        return ProductProxyPlafondBid::where('produit_id', $produitId)->where('membre_id', $membreId)->first();
    }

    public function saveProxy(int $produitId, int $membreId, string $plafond): ProductProxyPlafondBid
    {
        return ProductProxyPlafondBid::updateOrCreate(
            ['produit_id' => $produitId, 'membre_id' => $membreId],
            ['plafond' => $plafond, 'est_actif' => true],
        );
    }

    /** Plafonds actifs des autres membres, du plus haut au plus bas (le plus ancien gagne à égalité). */
    public function activeChallengers(int $produitId, int $excludeMembreId): Collection
    {
        return ProductProxyPlafondBid::where('produit_id', $produitId)
            ->where('est_actif', true)
            ->where('membre_id', '!=', $excludeMembreId)
            ->orderByDesc('plafond')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
    }

    public function deactivate(ProductProxyPlafondBid $proxy): void
    {
        $proxy->forceFill(['est_actif' => false])->save();
    }

    public function deactivateAll(int $produitId): void
    {
        ProductProxyPlafondBid::where('produit_id', $produitId)->update(['est_actif' => false]);
    }
}
