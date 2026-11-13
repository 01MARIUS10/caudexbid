<?php

namespace App\Modules\Product\_ProductAchat\Actions\DefineBuyNowPrice;

use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\_ProductAchat\Models\ProductAchat;
use App\Modules\Product\_ProductAchat\Repository\ProductAchatRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Shared\Exceptions\BusinessRuleException;
use App\Shared\Support\Money;

/**
 * Définit (ou modifie) le prix d'achat immédiat d'un lot non encore vendu.
 */
final class DefineBuyNowPriceAction
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly ProductAchatRepository $achats,
    ) {}

    public function execute(DefineBuyNowPriceInput $input): ProductAchat
    {
        $product = $this->products->find($input->produitId);

        if (! in_array($product->statut, [ProductStatus::Draft, ProductStatus::Active], true)) {
            throw new BusinessRuleException('Le lot n\'est plus en vente.');
        }
        if ($product->achat?->isBought()) {
            throw new BusinessRuleException('Ce lot a déjà été acheté.');
        }
        if (Money::toCents($input->prix) <= Money::toCents($product->prix_actuel ?? $product->prix_depart)) {
            throw new BusinessRuleException('Le prix d\'achat immédiat doit dépasser le prix actuel.');
        }

        return $this->achats->definePrice($product->id, Money::fromCents(Money::toCents($input->prix)));
    }
}
