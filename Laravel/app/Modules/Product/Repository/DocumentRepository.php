<?php

namespace App\Modules\Product\Repository;

use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\Models\Product;
use App\Modules\Product\_ProductBid\Models\ProductBid;
use App\Shared\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Données des 5 documents imprimables (A4 paysage), par session.
 *
 * $memberIds = null => tous les membres (admin) ; sinon périmètre limité aux adjudicataires / enchérisseurs donnés.
 * $members : membres du module User (clé = id, profil + parrain chargés), fournis par le controller via
 * ListMembersAction, pour afficher identifiants et parrains.
 */
class DocumentRepository
{
    /** 1. Liste des lots par session. */
    public function lots(int $sessionId): Collection
    {
        return Product::with('session', 'achat')
            ->where('session_enchere_id', $sessionId)
            ->where('statut', '!=', ProductStatus::Draft)
            ->orderBy('numero_lot')
            ->get()
            ->map(fn (Product $p) => [
                'date_enchere' => $p->session->debut_le->toDateString(),
                'nom' => $p->nom,
                'lot' => $p->numero_lot,
                'description_1' => $p->description_1,
                'prix_vente_existant' => $p->prix_vente_existant,
                'prix_base' => $p->prix_depart,
                'prix_achat_immediat' => $p->achat?->prix,
            ]);
    }

    /** 2. Résultat des adjudications par session. */
    public function adjudications(int $sessionId, ?array $memberIds, Collection $members): Collection
    {
        return $this->awarded($sessionId, $memberIds)->map(fn (Product $p) => [
            'date_enchere' => $p->session->debut_le->toDateString(),
            'nom' => $p->nom,
            'lot' => $p->numero_lot,
            'description_1' => $p->description_1,
            'prix_vente_existant' => $p->prix_vente_existant,
            'prix_adjudication' => $p->prixAdjudication(),
            'adjudicataire' => $members->get($p->adjudicataireId())?->identifiant,
        ]);
    }

    /** 3. Enchères en cours par session. */
    public function enchereEnCours(int $sessionId, ?array $memberIds, Collection $members): Collection
    {
        return ProductBid::with('produit.session')
            ->whereHas('produit', fn (Builder $q) => $q
                ->where('session_enchere_id', $sessionId)
                ->where('statut', ProductStatus::Active))
            ->when($memberIds !== null, fn (Builder $q) => $q->whereIn('membre_id', $memberIds))
            ->orderBy('produit_id')
            ->orderByDesc('montant')
            ->get()
            ->map(fn (ProductBid $b) => [
                'date_enchere' => $b->produit->session->debut_le->toDateString(),
                'nom' => $b->produit->nom,
                'lot' => $b->produit->numero_lot,
                'prix_vente_existant' => $b->produit->prix_vente_existant,
                'montant_enchere' => $b->montant,
                'encherisseur' => $members->get($b->membre_id)?->identifiant,
                'le' => $b->created_at,
            ]);
    }

    /** 4. Répartition par session (30 % propriétaire / 30 % premium / 40 % exploitant). */
    public function repartition(int $sessionId, ?array $memberIds): Collection
    {
        return $this->awarded($sessionId, $memberIds)->map(function (Product $p) {
            $shares = $this->shares($p);

            return [
                'date_enchere' => $p->session->debut_le->toDateString(),
                'nom' => $p->nom,
                'lot' => $p->numero_lot,
                'prix_vente_existant' => $p->prix_vente_existant,
                'prix_adjudication' => $p->prixAdjudication(),
                'part_premium' => $shares['premium'],
                'part_exploitant' => $shares['exploitant'],
                'part_proprietaire' => $shares['proprietaire'],
                'dividende_total_proprietaire' => $shares['dividende_total_proprietaire'],
            ];
        });
    }

    /** 5. Adjudications par membre premium (parrain de l'adjudicataire). */
    public function parPremium(int $sessionId, ?array $memberIds, Collection $members): Collection
    {
        return $this->awarded($sessionId, $memberIds)->map(function (Product $p) use ($members) {
            $adjudicataire = $members->get($p->adjudicataireId());

            return [
                'membre_premium' => $adjudicataire?->profile?->parrain?->identifiant,
                'adjudicataire' => $adjudicataire?->identifiant,
                'montant' => $p->prixAdjudication(),
                'lot' => $p->numero_lot,
            ];
        })->sortBy('membre_premium')->values();
    }

    /**
     * Calcul de la répartition. Base = prix de vente existant (ou prix de départ à défaut).
     * Dividende total propriétaire = base + 30 % de la différence.
     *
     * @return array{proprietaire: string, premium: string, exploitant: string, dividende_total_proprietaire: string}
     */
    public function shares(Product $p): array
    {
        $baseCents = Money::toCents($p->prix_vente_existant ?? $p->prix_depart);
        $diffCents = max(0, Money::toCents($p->prixAdjudication()) - $baseCents);
        $shares = config('caudexbid.shares');

        $proprietaire = Money::percent($diffCents, $shares['proprietaire']);
        $premium = Money::percent($diffCents, $shares['premium']);

        return [
            'proprietaire' => Money::fromCents($proprietaire),
            'premium' => Money::fromCents($premium),
            'exploitant' => Money::fromCents($diffCents - $proprietaire - $premium),
            'dividende_total_proprietaire' => Money::fromCents($baseCents + $proprietaire),
        ];
    }

    /** Lots adjugés / achetés de la session, filtrés sur les adjudicataires. */
    private function awarded(int $sessionId, ?array $memberIds): Collection
    {
        return Product::with('session', 'achat')
            ->where('session_enchere_id', $sessionId)
            ->whereIn('statut', [ProductStatus::Awarded, ProductStatus::Sold])
            ->orderBy('numero_lot')
            ->get()
            ->filter(fn (Product $p) => $memberIds === null || in_array($p->adjudicataireId(), $memberIds, true))
            ->values();
    }
}
