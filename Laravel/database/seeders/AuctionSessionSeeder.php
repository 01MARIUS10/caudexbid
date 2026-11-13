<?php

namespace Database\Seeders;

use App\Modules\Product\Models\Enums\AuctionSessionStatus;
use App\Modules\Product\Models\ProductAuctionSession;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * 3 sessions de 4 jours, clôture à 21h00 KST :
 * - la précédente (clôturée, lots adjugés non encore réglés) ;
 * - l'actuelle (ouverte, clôture demain soir => prix visibles, < 48 h) ;
 * - la prochaine (programmée, pour le bandeau « prochaine enchère »).
 */
class AuctionSessionSeeder extends Seeder
{
    public function run(): void
    {
        $tz = config('caudexbid.display_timezone');
        $currentClose = CarbonImmutable::now($tz)->addDay()->setTime(21, 0);

        $sessions = [
            [$currentClose->subDays(28), AuctionSessionStatus::Closed],
            [$currentClose, AuctionSessionStatus::Open],
            [$currentClose->addDays(28), AuctionSessionStatus::Scheduled],
        ];

        foreach ($sessions as [$close, $statut]) {
            ProductAuctionSession::create([
                'titre' => 'Session '.$close->format('Y-m'),
                'debut_le' => $close->subDays(4)->utc(),
                'cloture_le' => $close->utc(),
                'statut' => $statut,
            ]);
        }
    }
}
