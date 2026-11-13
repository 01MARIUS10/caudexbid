<?php

namespace App\Modules\Product\Actions\CreateProduct;

use App\Modules\Product\Models\Enums\AuctionSessionStatus;
use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\Models\Product;
use App\Modules\Product\_ProductAchat\Repository\ProductAchatRepository;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\DB;

/**
 * Les dates d'enchères sont identiques pour toutes les plantes : elles viennent de la session.
 */
final class CreateProductAction
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly ProductAuctionSessionRepository $sessions,
        private readonly ProductAchatRepository $achats,
    ) {}

    public function execute(CreateProductInput $input): Product
    {
        $session = $this->sessions->find($input->sessionId);

        if (in_array($session->statut, [AuctionSessionStatus::Closed, AuctionSessionStatus::Settled], true)) {
            throw new BusinessRuleException('Impossible d\'ajouter un lot à une session clôturée.');
        }

        return DB::transaction(function () use ($input, $session) {
            $product = $this->products->create($input->attributes + [
                'session_enchere_id' => $session->id,
                'debut_enchere_le' => $session->debut_le,
                'cloture_prevue_le' => $session->cloture_le,
                'cloture_effective_le' => $session->cloture_le,
                'statut' => $input->publier ? ProductStatus::Active : ProductStatus::Draft,
            ]);

            if ($input->prixAchatImmediat !== null) {
                $this->achats->definePrice($product->id, $input->prixAchatImmediat);
            }

            return $this->products->find($product->id);
        });
    }
}
