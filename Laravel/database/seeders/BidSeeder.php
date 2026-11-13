<?php

namespace Database\Seeders;

use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\Models\Product;
use App\Modules\Product\_ProductBid\Actions\PlaceBid\PlaceBidAction;
use App\Modules\Product\_ProductBid\Actions\PlaceBid\PlaceBidInput;
use App\Modules\User\_UserAuth\Models\UserAuth;
use Illuminate\Database\Seeder;

/**
 * Enchères de la session en cours, posées via PlaceBidAction
 * (montants cohérents, plafonds proxy résolus automatiquement).
 */
class BidSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::where('statut', ProductStatus::Active)->orderBy('numero_lot')->get()->values();
        $action = app(PlaceBidAction::class);

        // [index du lot, membre, montant, plafond proxy éventuel]
        $script = [
            [0, 'kenji', '100', null],
            [0, 'lina', '110', '200'],      // proxy de lina
            [0, 'kenji', '150', null],      // lina repasse devant automatiquement (160)
            [1, 'minho', '150', null],
            [1, 'andy88', '170', '300'],
            [1, 'kenji', '200', '250'],     // andy88 garde la tête à 260
            [2, 'lina', '80', null],
            [3, 'mika_p', '50', null],
            [3, 'minho', '60', '120'],
            [4, 'kenji', '300', null],
            [6, 'lina', '100', '180'],
            [6, 'minho', '120', null],      // lina reste devant (130)
        ];

        foreach ($script as [$index, $identifiant, $montant, $plafond]) {
            $membre = UserAuth::with('currentUserGrade.grade')->where('identifiant', $identifiant)->first();

            $action->execute(new PlaceBidInput(
                membre: $membre,
                produitId: $products[$index]->id,
                montant: $montant,
                proxy: $plafond !== null,
                plafond: $plafond,
            ));
        }
    }
}
