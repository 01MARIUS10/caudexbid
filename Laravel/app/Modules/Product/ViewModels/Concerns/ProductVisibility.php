<?php

namespace App\Modules\Product\ViewModels\Concerns;

use App\Modules\Product\Models\Product;
use App\Shared\Support\Money;

/**
 * Règles d'affichage communes aux écrans produit :
 * - prix actuel, prochaine enchère, nb d'enchérisseurs et achat immédiat : publiés seulement dans les 48 h avant clôture ;
 * - lot SOLD : seuls le numéro de produit et la mention SOLD sont publics ;
 *   le montant reste visible pour l'adjudicataire, son parrain et l'administrateur.
 *
 * Le spectateur (`$this->viewer`) et l'annuaire des membres (`$this->members`, pour retrouver le parrain de
 * l'adjudicataire) sont fournis à la construction.
 */
trait ProductVisibility
{
    protected function isSoldState(Product $product): bool
    {
        return $product->statut->isUnsettledSale();
    }

    protected function canSeePrices(Product $product): bool
    {
        return $this->viewer?->isAdmin() || $product->isPriceVisible();
    }

    /** Adjudicataire, parrain de l'adjudicataire ou administrateur. */
    protected function canSeeSettlementAmount(Product $product): bool
    {
        $viewer = $this->viewer;
        if (! $viewer) {
            return false;
        }
        if ($viewer->isAdmin()) {
            return true;
        }

        $winnerId = $product->adjudicataireId();

        return $winnerId !== null && ($winnerId === $viewer->id || $this->members->parrainId($winnerId) === $viewer->id);
    }

    protected function soldPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'numero_lot' => $product->numero_lot,
            'etat' => 'SOLD',
            'montant' => $this->when(
                $this->canSeeSettlementAmount($product),
                fn () => $product->prixAdjudication(),
            ),
        ];
    }

    protected function pricePayload(Product $product): array
    {
        $visible = $this->canSeePrices($product);

        return [
            'prix_visible' => $visible,
            'prix_actuel' => $this->when($visible, $product->prix_actuel),
            'prochaine_enchere_minimale' => $this->when($visible, fn () => Money::fromCents($product->prochaineEnchereMinimaleCents())),
            'nb_encherisseurs' => $this->when($visible, $product->nb_encherisseurs),
            'prix_achat_immediat' => $this->when($visible && $product->achat && ! $product->achat->isBought(), fn () => $product->achat->prix),
        ];
    }

    protected function timingPayload(Product $product): array
    {
        $tz = config('caudexbid.display_timezone');

        return [
            'cloture_le' => $product->cloture_effective_le->toIso8601String(),
            'cloture_le_kst' => $product->cloture_effective_le->copy()->tz($tz)->format('Y-m-d H:i'),
            'temps_restant_secondes' => max(0, (int) now()->diffInSeconds($product->cloture_effective_le, false)),
            'nb_prolongations' => $product->nb_prolongations,
        ];
    }
}
