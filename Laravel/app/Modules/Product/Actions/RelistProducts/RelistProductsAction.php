<?php

namespace App\Modules\Product\Actions\RelistProducts;

use App\Modules\Product\Models\Enums\AuctionSessionStatus;
use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductAuctionSession;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\_ProductAchat\Repository\ProductAchatRepository;
use App\Modules\Product\_ProductMedia\Repository\ProductMediaRepository;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\DB;

/**
 * Remet les lots d'une session précédente dans la session actuelle, comme NOUVEAUX lots :
 * - copie de la fiche (plante, propriétaire, nom, descriptions, tailles, prix de départ, pas, achat immédiat) ;
 * - copie des photos et de la vidéo (nouveaux fichiers) ;
 * - remis à zéro : enchères, prix actuel, enchérisseurs, prolongations ; dates = celles de la nouvelle session ;
 * - nouveau numéro de lot : préfixe de la session cible (aaMM-) + numéro suivant.
 * Les anciens lots ne sont pas modifiés (un lot adjugé reste à régler normalement).
 * Un lot déjà présent dans la session cible (même nom et même propriétaire) est ignoré.
 */
final class RelistProductsAction
{
    /** Colonnes recopiées de l'ancien lot. */
    private const COPIED = [
        'proprietaire_id', 'plante_id', 'categorie', 'nom',
        'description_1', 'description_2', 'description_3',
        'taille_diametre', 'taille_circonference', 'taille_hauteur',
        'prix_vente_existant', 'prix_depart', 'pas_enchere',
    ];

    public function __construct(
        private readonly ProductRepository $products,
        private readonly ProductAuctionSessionRepository $sessions,
        private readonly ProductAchatRepository $achats,
        private readonly ProductMediaRepository $media,
    ) {}

    /**
     * @return array{from: string, to: string, lots: list<array{ancien: string, nom: string, statut: string, nouveau: ?string, resultat: string, medias: int}>}
     *         resultat : cree | a_creer | deja_present
     */
    public function execute(RelistProductsInput $input): array
    {
        $to = $input->toSessionId ? $this->sessions->find($input->toSessionId) : $this->sessions->current();
        if (! $to) {
            throw new BusinessRuleException('Aucune session actuelle : créez d\'abord la nouvelle session d\'enchères.');
        }
        if (in_array($to->statut, [AuctionSessionStatus::Closed, AuctionSessionStatus::Settled], true)) {
            throw new BusinessRuleException("La session « {$to->titre} » est clôturée : impossible d'y ajouter des lots.");
        }

        $from = $input->fromSessionId ? $this->sessions->find($input->fromSessionId) : $this->sessions->previousWithProducts($to);
        if (! $from) {
            throw new BusinessRuleException('Aucune session précédente contenant des lots.');
        }

        $statuses = $input->all
            ? [ProductStatus::Unsold, ProductStatus::Awarded, ProductStatus::Sold, ProductStatus::Active, ProductStatus::Draft]
            : [ProductStatus::Unsold];

        $prefix = $to->cloture_le->format('ym').'-';
        $next = $this->nextLotNumber($prefix);
        $lots = [];

        foreach ($this->products->toRelist($from->id, $statuses) as $old) {
            $line = ['ancien' => $old->numero_lot, 'nom' => $old->nom, 'statut' => $old->statut->value, 'nouveau' => null, 'resultat' => 'deja_present', 'medias' => $old->media->count()];

            if (! $this->products->existsInSession($to->id, $old->nom, $old->proprietaire_id)) {
                $line['nouveau'] = sprintf('%s%02d', $prefix, $next++);
                $line['resultat'] = $input->dryRun ? 'a_creer' : 'cree';
                if (! $input->dryRun) {
                    $line['medias'] = $this->relist($old, $to, $line['nouveau'], $input->draft);
                }
            }

            $lots[] = $line;
        }

        return ['from' => $from->titre, 'to' => $to->titre, 'lots' => $lots];
    }

    /** Crée le nouveau lot et copie ses médias. Retourne le nombre de médias copiés. */
    private function relist(Product $old, ProductAuctionSession $to, string $numero, bool $draft): int
    {
        return DB::transaction(function () use ($old, $to, $numero, $draft) {
            $new = $this->products->create($old->only(self::COPIED) + [
                'session_enchere_id' => $to->id,
                'numero_lot' => $numero,
                'debut_enchere_le' => $to->debut_le,
                'cloture_prevue_le' => $to->cloture_le,
                'cloture_effective_le' => $to->cloture_le,
                'statut' => $draft ? ProductStatus::Draft : ProductStatus::Active,
            ]);

            if ($old->achat) {
                $this->achats->definePrice($new->id, $old->achat->prix);
            }

            $copied = 0;
            foreach ($old->media as $media) {
                $copied += $this->media->copyTo($media, $new->id) ? 1 : 0;
            }

            return $copied;
        });
    }

    private function nextLotNumber(string $prefix): int
    {
        $max = 0;
        foreach ($this->products->lotNumbersWithPrefix($prefix) as $numero) {
            $max = max($max, (int) substr($numero, strlen($prefix)));
        }

        return $max + 1;
    }
}
