<?php

namespace App\Modules\Product\Actions\CloseExpiredAuctions;

use App\Modules\Product\Actions\NotifyAward\NotifyAwardAction;
use App\Modules\Product\Actions\NotifyAward\NotifyAwardInput;
use App\Modules\Product\Models\Enums\AuctionSessionStatus;
use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\_ProductBid\Repository\ProductBidRepository;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Support\Facades\DB;

/**
 * Tâche planifiée (chaque minute) :
 * - ouvre les sessions dont la date de début est atteinte ;
 * - clôture les lots dont l'heure de clôture effective (après soft close) est dépassée :
 *   adjugé s'il y a un auteur du prix actuel, sinon non vendu ;
 * - clôture les sessions sans lot encore actif.
 */
final class CloseExpiredAuctionsAction
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly ProductAuctionSessionRepository $sessions,
        private readonly ProductBidRepository $bids,
        private readonly NotifyAwardAction $notifyAward,
    ) {}

    /** @return array{sessions_ouvertes: int, adjuges: int, non_vendus: int, sessions_cloturees: int} */
    public function execute(CloseExpiredAuctionsInput $input): array
    {
        $result = ['sessions_ouvertes' => 0, 'adjuges' => 0, 'non_vendus' => 0, 'sessions_cloturees' => 0];

        foreach ($this->sessions->dueToOpen($input->now) as $session) {
            $this->sessions->updateStatus($session, AuctionSessionStatus::Open);
            $result['sessions_ouvertes']++;
        }

        foreach ($this->products->expiredActive($input->now) as $productId) {
            $awarded = DB::transaction(function () use ($productId, $input, &$result) {
                $product = $this->products->findForUpdate($productId);

                if (! $product || $product->statut !== ProductStatus::Active || $product->cloture_effective_le->gt($input->now)) {
                    return false;
                }

                $isAwarded = $product->auteur_prix_actuel_id !== null;
                $this->products->update($product, [
                    'statut' => $isAwarded ? ProductStatus::Awarded : ProductStatus::Unsold,
                ]);
                $this->bids->deactivateAll($product->id);
                $result[$isAwarded ? 'adjuges' : 'non_vendus']++;

                return $isAwarded;
            });

            if ($awarded) {
                $this->notifyAward->execute(new NotifyAwardInput($productId));
            }
        }

        foreach ($this->sessions->openPastClosing($input->now) as $session) {
            if (! $this->products->sessionHasStatus($session->id, ProductStatus::Active)) {
                $this->sessions->updateStatus($session, AuctionSessionStatus::Closed);
                $result['sessions_cloturees']++;
            }
        }

        return $result;
    }
}
