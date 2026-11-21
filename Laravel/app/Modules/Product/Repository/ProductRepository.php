<?php

namespace App\Modules\Product\Repository;

use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\Models\Product;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProductRepository
{
    /** Relations affichées sur une carte / un écran de détail. */
    /** Relations lues par les ViewModels (aucune requête n'est faite à l'affichage). */
    public const DISPLAY_RELATIONS = ['media', 'session', 'achat'];

    public function find(int $id): ?Product
    {
        return Product::with(self::DISPLAY_RELATIONS)->find($id);
    }

    /** Verrou de ligne pour sérialiser les enchères / achats concurrents. */
    public function findForUpdate(int $id): ?Product
    {
        return Product::whereKey($id)->lockForUpdate()->first();
    }

    public function create(array $attributes): Product
    {
        return Product::create($attributes);
    }

    public function update(Product $product, array $attributes): Product
    {
        $product->fill($attributes)->save();

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    /** Liste paginée (back office). Filtres : session, statut, recherche lot / nom, plante de la collection. */
    public function search(?int $sessionId = null, ?ProductStatus $statut = null, ?string $q = null, int $perPage = 25, ?int $planteId = null): LengthAwarePaginator
    {
        return Product::with(self::DISPLAY_RELATIONS)
            ->when($sessionId, fn (Builder $b) => $b->where('session_enchere_id', $sessionId))
            ->when($statut, fn (Builder $b) => $b->where('statut', $statut))
            ->when($planteId, fn (Builder $b) => $b->where('plante_id', $planteId))
            ->when($q, fn (Builder $b) => $b->where(fn (Builder $w) => $w
                ->where('numero_lot', 'like', "%{$q}%")
                ->orWhere('nom', 'like', "%{$q}%")))
            ->orderByDesc('session_enchere_id')
            ->orderBy('numero_lot')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @return array<string, int> nombre de lots par statut */
    public function countByStatus(): array
    {
        return Product::selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut')
            ->mapWithKeys(fn ($total, $statut) => [($statut instanceof ProductStatus ? $statut->value : $statut) => (int) $total])
            ->all();
    }

    /** Vitrine publique : tous les lots publiés (hors brouillons), sans données de vente. */
    public function published(): Collection
    {
        return Product::with('media')
            ->where('statut', '!=', ProductStatus::Draft)
            ->orderByDesc('session_enchere_id')
            ->orderBy('numero_lot')
            ->get();
    }

    /**
     * Lots d'une session à remettre en vente (commande auctions:relist), médias et achat immédiat chargés.
     *
     * @param  list<ProductStatus>  $statuses
     */
    public function toRelist(int $sessionId, array $statuses): Collection
    {
        return Product::with(['media', 'achat'])
            ->where('session_enchere_id', $sessionId)
            ->whereIn('statut', $statuses)
            ->orderBy('numero_lot')
            ->get();
    }

    /** Le lot existe-t-il déjà dans la session (même nom, même propriétaire) ? Évite de remettre deux fois le même lot. */
    public function existsInSession(int $sessionId, string $nom, int $proprietaireId): bool
    {
        return Product::where('session_enchere_id', $sessionId)
            ->where('nom', $nom)
            ->where('proprietaire_id', $proprietaireId)
            ->exists();
    }

    /** @return list<string> numéros de lot commençant par le préfixe (ex. « 2612- ») */
    public function lotNumbersWithPrefix(string $prefix): array
    {
        return Product::where('numero_lot', 'like', $prefix.'%')->pluck('numero_lot')->all();
    }

    /** Lots publiés d'une session (« Enchères de cette session »). */
    public function forSession(int $sessionId): Collection
    {
        return Product::with(self::DISPLAY_RELATIONS)
            ->where('session_enchere_id', $sessionId)
            ->where('statut', '!=', ProductStatus::Draft)
            ->orderBy('numero_lot')
            ->get();
    }

    /**
     * Adjudications non soldées (produit adjugé ou acheté, pas encore réglé).
     *
     * @param  list<int>|null  $adjudicataireIds  null = tous (admin)
     */
    public function unsettled(?array $adjudicataireIds = null): Collection
    {
        return $this->unsettledQuery($adjudicataireIds)
            ->with(self::DISPLAY_RELATIONS)
            ->orderBy('cloture_effective_le')
            ->get();
    }

    public function hasUnsettledAwardFor(int $userId): bool
    {
        return $this->unsettledQuery([$userId])->exists();
    }

    /** Lots actifs dont l'heure de clôture effective est dépassée. */
    public function expiredActive(CarbonInterface $now): Collection
    {
        return Product::where('statut', ProductStatus::Active)
            ->where('cloture_effective_le', '<=', $now)
            ->pluck('id');
    }

    public function sessionHasStatus(int $sessionId, ProductStatus ...$statuses): bool
    {
        return Product::where('session_enchere_id', $sessionId)
            ->whereIn('statut', $statuses)
            ->exists();
    }

    private function unsettledQuery(?array $adjudicataireIds): Builder
    {
        return Product::query()->where(function (Builder $q) use ($adjudicataireIds) {
            $q->where(function (Builder $awarded) use ($adjudicataireIds) {
                $awarded->where('statut', ProductStatus::Awarded);
                if ($adjudicataireIds !== null) {
                    $awarded->whereIn('auteur_prix_actuel_id', $adjudicataireIds);
                }
            })->orWhere(function (Builder $sold) use ($adjudicataireIds) {
                $sold->where('statut', ProductStatus::Sold);
                if ($adjudicataireIds !== null) {
                    $sold->whereHas('achat', fn (Builder $a) => $a->whereIn('acheteur_id', $adjudicataireIds));
                }
            });
        });
    }
}
