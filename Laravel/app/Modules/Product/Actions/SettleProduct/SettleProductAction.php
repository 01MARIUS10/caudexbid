<?php

namespace App\Modules\Product\Actions\SettleProduct;

use App\Modules\Product\Models\Enums\AuctionSessionStatus;
use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\_ProductMedia\Repository\ProductMediaRepository;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\DB;

/**
 * Règlement terminé : les informations du produit sont supprimées des tables « Produit »
 * et « Informations d'enchère » (cascade : médias, enchères, plafonds, achat immédiat).
 */
final class SettleProductAction
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly ProductMediaRepository $media,
        private readonly ProductAuctionSessionRepository $sessions,
    ) {}

    public function execute(SettleProductInput $input): void
    {
        DB::transaction(function () use ($input) {
            $product = $this->products->findForUpdate($input->produitId);

            if (! $product->statut->isUnsettledSale()) {
                throw new BusinessRuleException('Seul un lot adjugé ou vendu peut être réglé.');
            }

            $sessionId = $product->session_enchere_id;
            $this->media->deleteFilesOf($product->id);
            $this->products->delete($product);

            $session = $this->sessions->find($sessionId);
            if ($session->statut === AuctionSessionStatus::Closed
                && ! $this->products->sessionHasStatus($sessionId, ProductStatus::Awarded, ProductStatus::Sold, ProductStatus::Active)) {
                $this->sessions->updateStatus($session, AuctionSessionStatus::Settled);
            }
        });
    }
}
