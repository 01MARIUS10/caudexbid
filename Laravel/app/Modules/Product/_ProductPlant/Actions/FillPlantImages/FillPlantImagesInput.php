<?php

namespace App\Modules\Product\_ProductPlant\Actions\FillPlantImages;

final readonly class FillPlantImagesInput
{
    public function __construct(
        /** Remplacer aussi les images déjà présentes (sinon : seulement les plantes sans image). */
        public bool $all = false,
        /** Simulation : n'écrit rien, liste ce qui serait fait. */
        public bool $dryRun = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self((bool) ($data['all'] ?? false), (bool) ($data['dry_run'] ?? false));
    }
}
