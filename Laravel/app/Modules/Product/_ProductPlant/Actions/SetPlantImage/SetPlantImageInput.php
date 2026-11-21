<?php

namespace App\Modules\Product\_ProductPlant\Actions\SetPlantImage;

use Illuminate\Http\UploadedFile;

final readonly class SetPlantImageInput
{
    public function __construct(
        public int $planteId,
        public UploadedFile $image,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self((int) $data['plante_id'], $data['image']);
    }
}
