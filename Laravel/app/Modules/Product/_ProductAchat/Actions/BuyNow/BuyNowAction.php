<?php

namespace App\Modules\Product\_ProductAchat\Actions\BuyNow;

use App\Modules\Product\Actions\NotifyAward\NotifyAwardAction;
use App\Modules\Product\Actions\NotifyAward\NotifyAwardInput;
use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\Models\Product;
use App\Modules\Product\_ProductAchat\Repository\ProductAchatRepository;
use App\Modules\Product\_ProductBid\Repository\ProductBidRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\DB;

/**
 * Achat immédiat : l'enchère est interrompue, le lot passe en SOLD.
 * Un seul achat par produit (verrou sur le produit et sur sa ligne product_achat).
 */
final class BuyNowAction
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly ProductAchatRepository $achats,
        private readonly ProductBidRepository $bids,
        private readonly NotifyAwardAction $notifyAward,
    ) {}

    public function execute(BuyNowInput $input): Product
    {
        $product = DB::transaction(function () use ($input) {
            $product = $this->products->findForUpdate($input->produitId)
                ?? throw new BusinessRuleException('Produit introuvable.', 404);
            $achat = $this->achats->findByProduitForUpdate($product->id);

            if (! $achat) {
                throw new BusinessRuleException('Ce lot ne propose pas d\'achat immédiat.');
            }
            if ($achat->isBought() || $product->statut === ProductStatus::Sold) {
                throw new BusinessRuleException('Ce lot a déjà été acheté.');
            }
            if (! $product->isOpenForBids()) {
                throw new BusinessRuleException('Les enchères ne sont pas ouvertes pour ce lot.');
            }
            if (! $product->isPriceVisible()) {
                throw new BusinessRuleException('L\'achat immédiat n\'est possible que dans les 48 heures précédant la clôture.');
            }

            $this->achats->markBought($achat, $input->acheteur->id);
            $this->products->update($product, ['statut' => ProductStatus::Sold]);
            $this->bids->deactivateAll($product->id);

            return $product;
        });

        $this->notifyAward->execute(new NotifyAwardInput($product->id));

        return $this->products->find($product->id);
    }
}
