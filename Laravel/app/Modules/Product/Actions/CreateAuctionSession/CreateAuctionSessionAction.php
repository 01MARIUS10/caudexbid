<?php

namespace App\Modules\Product\Actions\CreateAuctionSession;

use App\Modules\Product\Models\Enums\AuctionSessionStatus;
use App\Modules\Product\Models\ProductAuctionSession;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;

final class CreateAuctionSessionAction
{
    public function __construct(private readonly ProductAuctionSessionRepository $sessions) {}

    public function execute(CreateAuctionSessionInput $input): ProductAuctionSession
    {
        return $this->sessions->create([
            'titre' => $input->titre ?? 'Session '.$input->debutLe->tz(config('caudexbid.display_timezone'))->format('Y-m'),
            'debut_le' => $input->debutLe,
            'cloture_le' => $input->clotureLe,
            'statut' => $input->debutLe->isPast() ? AuctionSessionStatus::Open : AuctionSessionStatus::Scheduled,
        ]);
    }
}
