<?php

namespace App\Modules\Product\_ProductMedia\Actions\AddProductMedia;

use App\Modules\Product\_ProductMedia\Models\Enums\MediaType;
use App\Modules\Product\_ProductMedia\Models\Enums\PhotoView;
use Illuminate\Http\UploadedFile;

final readonly class AddProductMediaInput
{
    public function __construct(
        public int $produitId,
        public MediaType $type,
        public int $position,
        public ?PhotoView $vue,
        public UploadedFile $fichier,
        public ?int $dureeSecondes,
        public bool $estVitrine,
    ) {}

    public static function fromArray(array $data): self
    {
        $type = MediaType::from($data['type']);

        return new self(
            produitId: (int) $data['produit_id'],
            type: $type,
            position: $type === MediaType::Video ? 1 : (int) $data['position'],
            vue: isset($data['vue']) ? PhotoView::from($data['vue']) : null,
            fichier: $data['fichier'],
            dureeSecondes: isset($data['duree_secondes']) ? (int) $data['duree_secondes'] : null,
            estVitrine: $type === MediaType::Photo && (bool) ($data['est_vitrine'] ?? false),
        );
    }
}
