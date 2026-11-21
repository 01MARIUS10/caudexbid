<?php

namespace App\Modules\Product\_ProductPlant\Actions\FillPlantImages;

use App\Modules\Product\_ProductMedia\Repository\ProductMediaRepository;
use App\Modules\Product\_ProductPlant\Repository\ProductPlantRepository;
use Illuminate\Support\Facades\Storage;

/**
 * Complète l'image des plantes de la collection en copiant une photo d'un produit lié
 * (photo 1 en priorité, produit le plus récent). Le fichier est copié : il survit au règlement du produit.
 */
final class FillPlantImagesAction
{
    public function __construct(
        private readonly ProductPlantRepository $plants,
        private readonly ProductMediaRepository $media,
    ) {}

    /** @return list<array{plante: string, statut: string, source: ?string}> statut : copiee | a_copier | sans_photo | fichier_absent */
    public function execute(FillPlantImagesInput $input): array
    {
        $disk = Storage::disk(config('caudexbid.media.disk'));
        $report = [];

        foreach ($this->plants->forImageFill($input->all) as $plant) {
            $photo = $this->media->firstPhotoOfPlant($plant->id);

            $statut = match (true) {
                $photo === null => 'sans_photo',
                ! $disk->exists($photo->chemin) => 'fichier_absent',
                $input->dryRun => 'a_copier',
                default => 'copiee',
            };

            if ($statut === 'copiee') {
                $this->plants->copyImageFrom($plant, $photo->chemin);
            }

            $report[] = ['plante' => $plant->nom, 'statut' => $statut, 'source' => $photo?->chemin];
        }

        return $report;
    }
}
