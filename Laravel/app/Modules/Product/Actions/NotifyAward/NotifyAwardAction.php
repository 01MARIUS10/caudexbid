<?php

namespace App\Modules\Product\Actions\NotifyAward;

use App\Modules\Message\Actions\SendSystemMessage\SendSystemMessageAction;
use App\Modules\Message\Actions\SendSystemMessage\SendSystemMessageInput;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersInput;
use App\Shared\Support\Money;

/**
 * Notification d'adjudication : envoyée à l'adjudicataire et au membre premium, son parrain.
 */
final class NotifyAwardAction
{
    public function __construct(
        private readonly NotifyAwardValidation $validation,
        private readonly ProductRepository $products,
        private readonly ListMembersAction $listMembers,
        private readonly SendSystemMessageAction $sendSystemMessage,
    ) {}

    public function execute(NotifyAwardInput $input): void
    {
        $this->validation->validate($input);

        $product = $this->products->find($input->produitId);
        $winnerId = $product?->adjudicataireId();

        if (! $product || ! $winnerId) {
            return;
        }

        $winner = $this->listMembers->execute(new ListMembersInput(ids: [$winnerId]))->first();
        $prix = Money::format($product->prixAdjudication());

        $this->sendSystemMessage->execute(new SendSystemMessageInput($winnerId, sprintf(
            'Félicitations : le lot %s (%s) vous est adjugé pour %s.',
            $product->numero_lot,
            $product->nom,
            $prix,
        )));

        $parrainId = $winner?->profile?->parrain_id;
        if ($parrainId) {
            $this->sendSystemMessage->execute(new SendSystemMessageInput($parrainId, sprintf(
                'Votre filleul %s a remporté le lot %s (%s) pour %s. Livraison et règlement sous votre responsabilité.',
                $winner->identifiant,
                $product->numero_lot,
                $product->nom,
                $prix,
            )));
        }
    }
}
