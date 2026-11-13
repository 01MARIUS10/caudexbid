<?php

namespace App\Modules\Product\_ProductMedia\Actions\AddProductMedia;

use App\Modules\Product\_ProductMedia\Models\ProductMedia;
use App\Modules\Product\_ProductMedia\Repository\ProductMediaRepository;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\DB;

/**
 * Ajoute (ou remplace) la photo n°1-4 ou la vidéo d'un produit.
 */
final class AddProductMediaAction
{
    public function __construct(private readonly ProductMediaRepository $media) {}

    public function execute(AddProductMediaInput $input): ProductMedia
    {
        $existing = $this->media->findSlot($input->produitId, $input->type, $input->position);

        if ($input->estVitrine && $this->media->countShowcase($existing?->id) >= config('caudexbid.media.showcase_max')) {
            throw new BusinessRuleException('L\'écran d\'accueil affiche au maximum '.config('caudexbid.media.showcase_max').' photos.');
        }

        $path = $input->fichier->store('products/'.$input->produitId, config('caudexbid.media.disk'));

        return DB::transaction(function () use ($input, $existing, $path) {
            if ($existing) {
                $this->media->delete($existing);
            }

            return $this->media->create([
                'produit_id' => $input->produitId,
                'type' => $input->type,
                'position' => $input->position,
                'vue' => $input->vue,
                'chemin' => $path,
                'duree_secondes' => $input->dureeSecondes,
                'est_vitrine' => $input->estVitrine,
            ]);
        });
    }
}
