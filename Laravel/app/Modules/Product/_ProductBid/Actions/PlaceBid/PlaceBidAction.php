<?php

namespace App\Modules\Product\_ProductBid\Actions\PlaceBid;

use App\Modules\Product\Models\Product;
use App\Modules\Product\_ProductBid\Models\Enums\BidSource;
use App\Modules\Product\_ProductBid\Models\ProductBid;
use App\Modules\Product\_ProductBid\Models\ProductProxyPlafondBid;
use App\Modules\Product\_ProductBid\Repository\ProductBidRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Shared\Exceptions\BusinessRuleException;
use App\Shared\Support\Money;
use Illuminate\Support\Facades\DB;

/**
 * Pose d'une enchère :
 * 1. montant >= prochaine enchère minimale, par pas d'enchère ;
 * 2. enregistrement du plafond si « Proxy bid » est coché ;
 * 3. résolution des enchères automatiques (le système surenchérit pour les plafonds concurrents) ;
 * 4. soft close : enchère dans les 5 dernières minutes => +5 min, 6 fois max.
 */
final class PlaceBidAction
{
    private const MAX_PROXY_ROUNDS = 100;

    public function __construct(
        private readonly ProductRepository $products,
        private readonly ProductBidRepository $bids,
    ) {}

    /** @return array{product: Product, bid: ?ProductBid, est_meilleur_encherisseur: bool, prolongation: bool} */
    public function execute(PlaceBidInput $input): array
    {
        return DB::transaction(function () use ($input) {
            $product = $this->products->findForUpdate($input->produitId)
                ?? throw new BusinessRuleException('Produit introuvable.', 404);

            $now = now();
            if (! $product->isOpenForBids($now)) {
                throw new BusinessRuleException('Les enchères ne sont pas ouvertes pour ce lot.');
            }

            $membreId = $input->membre->id;

            // Le meilleur enchérisseur ne surenchérit pas sur lui-même : il peut seulement relever son plafond.
            if ($product->auteur_prix_actuel_id === $membreId) {
                return $this->raiseOwnPlafond($product, $input);
            }

            $pas = Money::toCents($product->pas_enchere);
            $minimum = $product->prochaineEnchereMinimaleCents();
            $montant = Money::toCents($input->montant);

            if ($montant < $minimum) {
                throw new BusinessRuleException('Le montant doit être au moins de '.Money::fromCents($minimum).'.');
            }
            if (($montant - $minimum) % $pas !== 0) {
                throw new BusinessRuleException('Le montant doit suivre le pas d\'enchère ('.$product->pas_enchere.').');
            }

            $proxy = $input->proxy ? $this->bids->saveProxy($product->id, $membreId, $input->plafond) : null;

            $bid = $this->bids->create([
                'produit_id' => $product->id,
                'membre_id' => $membreId,
                'montant' => Money::fromCents($montant),
                'pas_enchere' => $product->pas_enchere,
                'origine' => BidSource::Manual,
                'enchere_auto_id' => $proxy?->id,
                'a_prolonge' => false,
            ]);

            [$leaderId, $price] = $this->resolveProxies($product, $membreId, $proxy, $montant, $pas);

            $prolongation = $this->applySoftClose($product, $bid, $now);

            $this->products->update($product, [
                'prix_actuel' => Money::fromCents($price),
                'auteur_prix_actuel_id' => $leaderId,
                'nb_encherisseurs' => $this->bids->distinctBidderCount($product->id),
            ]);

            return [
                'product' => $product,
                'bid' => $bid,
                'est_meilleur_encherisseur' => $leaderId === $membreId,
                'prolongation' => $prolongation,
            ];
        });
    }

    /**
     * Les plafonds actifs des concurrents surenchérissent automatiquement, au minimum nécessaire.
     * À plafond égal, le plafond enregistré le plus tôt l'emporte.
     *
     * @return array{0: int, 1: int} [id du meilleur enchérisseur, prix actuel en centimes]
     */
    private function resolveProxies(Product $product, int $leaderId, ?ProductProxyPlafondBid $leaderProxy, int $price, int $pas): array
    {
        for ($round = 0; $round < self::MAX_PROXY_ROUNDS; $round++) {
            $challenger = null;
            foreach ($this->bids->activeChallengers($product->id, $leaderId) as $proxy) {
                if (Money::toCents($proxy->plafond) < $price + $pas) {
                    $this->bids->deactivate($proxy); // plafond dépassé
                    continue;
                }
                $challenger ??= $proxy;
            }

            if (! $challenger) {
                break;
            }

            $challengerMax = Money::toCents($challenger->plafond);
            $leaderMax = $leaderProxy?->est_actif ? Money::toCents($leaderProxy->plafond) : $price;
            $challengerWinsTie = $leaderProxy !== null && $challenger->created_at->lt($leaderProxy->created_at);

            if ($challengerMax > $leaderMax || ($challengerMax === $leaderMax && $challengerWinsTie)) {
                // Le concurrent passe devant.
                $newPrice = min($challengerMax, $leaderMax + $pas);
                if ($leaderProxy && $leaderMax > $price && $newPrice > $leaderMax) {
                    $this->proxyBid($product, $leaderId, $leaderProxy, $leaderMax);
                }
                if ($leaderProxy) {
                    $this->bids->deactivate($leaderProxy);
                }
                $this->proxyBid($product, $challenger->membre_id, $challenger, $newPrice);

                $leaderId = $challenger->membre_id;
                $leaderProxy = $challenger;
                $price = $newPrice;
            } else {
                // Le plafond du meilleur enchérisseur résiste : le concurrent atteint son plafond.
                $this->proxyBid($product, $challenger->membre_id, $challenger, $challengerMax);
                $this->bids->deactivate($challenger);

                $price = min($leaderMax, $challengerMax + $pas);
                $this->proxyBid($product, $leaderId, $leaderProxy, $price);
            }
        }

        return [$leaderId, $price];
    }

    private function proxyBid(Product $product, int $membreId, ProductProxyPlafondBid $proxy, int $amount): ProductBid
    {
        return $this->bids->create([
            'produit_id' => $product->id,
            'membre_id' => $membreId,
            'montant' => Money::fromCents($amount),
            'pas_enchere' => $product->pas_enchere,
            'origine' => BidSource::Proxy,
            'enchere_auto_id' => $proxy->id,
            'a_prolonge' => false,
        ]);
    }

    /** Enchère dans les 5 dernières minutes : clôture prolongée de 5 min, 6 fois maximum. */
    private function applySoftClose(Product $product, ProductBid $bid, $now): bool
    {
        $cfg = config('caudexbid.soft_close');
        $windowStart = $product->cloture_effective_le->copy()->subMinutes($cfg['window_minutes']);

        if ($now->lt($windowStart) || $product->nb_prolongations >= $cfg['max_extensions']) {
            return false;
        }

        $this->products->update($product, [
            'cloture_effective_le' => $product->cloture_effective_le->copy()->addMinutes($cfg['extension_minutes']),
            'nb_prolongations' => $product->nb_prolongations + 1,
        ]);
        $this->bids->markExtension($bid);

        return true;
    }

    private function raiseOwnPlafond(Product $product, PlaceBidInput $input): array
    {
        if (! $input->proxy) {
            throw new BusinessRuleException('Vous êtes déjà le meilleur enchérisseur sur ce lot.');
        }
        if (Money::toCents($input->plafond) <= Money::toCents($product->prix_actuel)) {
            throw new BusinessRuleException('Le plafond doit être supérieur au prix actuel.');
        }

        $this->bids->saveProxy($product->id, $input->membre->id, $input->plafond);

        return [
            'product' => $product,
            'bid' => null,
            'est_meilleur_encherisseur' => true,
            'prolongation' => false,
        ];
    }
}
