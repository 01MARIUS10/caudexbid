<?php

namespace Database\Seeders;

use App\Modules\Product\Models\Enums\AuctionSessionStatus;
use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductAuctionSession;
use App\Modules\Product\Models\ProductOwner;
use App\Modules\Product\_ProductAchat\Models\ProductAchat;
use App\Modules\User\_UserAuth\Models\UserAuth;
use Illuminate\Database\Seeder;

/**
 * Lots de démonstration (les photos et vidéos sont ajoutées par ProductMediaSeeder).
 * Session actuelle : 10 lots actifs. Session précédente : 2 adjugés + 1 acheté (non réglés).
 */
class ProductSeeder extends Seeder
{
    private const PLANTS = [
        // nom, description 1 (taille), diamètre, circonférence, hauteur, prix existant, départ, pas, achat immédiat
        ['Pachypodium gracilius', 'Caudex 12 cm, port compact', 12, 38, 18, 180, 100, 10, 400],
        ['Operculicarya pachypus', 'Caudex 9 cm, branches torsadées', 9, 28, 22, 250, 150, 10, 600],
        ['Pachypodium windsorii', 'Caudex 7 cm, floraison rouge', 7, 22, 15, 120, 80, 5, null],
        ['Adenia glauca', 'Caudex 14 cm, tige verte', 14, 44, 30, 90, 50, 5, 250],
        ['Pachypodium brevicaule', 'Caudex 16 cm, très plat', 16, 50, 8, 420, 300, 20, 1000],
        ['Uncarina roeoesliana', 'Caudex 10 cm, fleurs jaunes', 10, 31, 35, 110, 70, 5, null],
        ['Operculicarya decaryi', 'Caudex 11 cm, bonsaï naturel', 11, 35, 28, 160, 100, 10, 450],
        ['Pachypodium densiflorum', 'Caudex 13 cm, multi-têtes', 13, 41, 20, 140, 90, 10, null],
        ['Euphorbia ankarensis', 'Tige 6 cm, épines rouges', 6, 19, 25, 75, 40, 5, 200],
        ['Pachypodium rosulatum', 'Caudex 15 cm, port arbustif', 15, 47, 32, 200, 120, 10, 500],
    ];

    public function run(): void
    {
        $sessions = ProductAuctionSession::orderBy('debut_le')->get();
        $previous = $sessions->firstWhere('statut', AuctionSessionStatus::Closed);
        $current = $sessions->firstWhere('statut', AuctionSessionStatus::Open);
        $owners = ProductOwner::pluck('id')->all();

        foreach (self::PLANTS as $i => $plant) {
            $lot = sprintf('%s-%02d', $current->cloture_le->format('ym'), $i + 1);
            $this->createProduct($current, $owners[$i % 2], $lot, $plant, ProductStatus::Active);
        }

        $this->seedPreviousSession($previous, $owners);
    }

    private function createProduct(ProductAuctionSession $session, int $ownerId, string $lot, array $plant, ProductStatus $statut): Product
    {
        [$nom, $desc1, $diam, $circ, $haut, $existant, $depart, $pas, $achat] = $plant;

        $product = Product::create([
            'session_enchere_id' => $session->id,
            'proprietaire_id' => $ownerId,
            'categorie' => 'plant',
            'numero_lot' => $lot,
            'nom' => $nom,
            'description_1' => $desc1,
            'description_2' => 'Origine : Madagascar. Plante établie, racines saines.',
            'description_3' => 'Documents CITES fournis.',
            'taille_diametre' => $diam,
            'taille_circonference' => $circ,
            'taille_hauteur' => $haut,
            'prix_vente_existant' => $existant,
            'prix_depart' => $depart,
            'pas_enchere' => $pas,
            'debut_enchere_le' => $session->debut_le,
            'cloture_prevue_le' => $session->cloture_le,
            'cloture_effective_le' => $session->cloture_le,
            'statut' => $statut,
        ]);

        if ($achat !== null) {
            ProductAchat::create(['produit_id' => $product->id, 'prix' => $achat]);
        }

        return $product;
    }

    /** Lots de la session précédente : adjugés ou achetés, en attente de règlement. */
    private function seedPreviousSession(ProductAuctionSession $session, array $owners): void
    {
        $kenji = UserAuth::where('identifiant', 'kenji')->value('id');
        $lina = UserAuth::where('identifiant', 'lina')->value('id');
        $minho = UserAuth::where('identifiant', 'minho')->value('id');
        $prefix = $session->cloture_le->format('ym');

        $awarded1 = $this->createProduct($session, $owners[0], $prefix.'-01', ['Pachypodium eburneum', 'Caudex 10 cm, fleurs blanches', 10, 31, 12, 200, 120, 10, null], ProductStatus::Awarded);
        $awarded1->update(['prix_actuel' => 340, 'auteur_prix_actuel_id' => $kenji, 'nb_encherisseurs' => 3]);

        $awarded2 = $this->createProduct($session, $owners[1], $prefix.'-02', ['Operculicarya hyphaenoides', 'Caudex 8 cm, feuillage fin', 8, 25, 26, 150, 90, 10, null], ProductStatus::Awarded);
        $awarded2->update(['prix_actuel' => 210, 'auteur_prix_actuel_id' => $lina, 'nb_encherisseurs' => 2]);

        $sold = $this->createProduct($session, $owners[0], $prefix.'-03', ['Pachypodium horombense', 'Caudex 12 cm, fleurs jaunes', 12, 37, 20, 180, 110, 10, 380], ProductStatus::Sold);
        $sold->update(['prix_actuel' => 150, 'nb_encherisseurs' => 1]);
        ProductAchat::where('produit_id', $sold->id)->update([
            'acheteur_id' => $minho,
            'achete_le' => $session->cloture_le->copy()->subHours(30),
        ]);
    }
}
