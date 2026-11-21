<?php

namespace App\Modules\Product\_ProductPlant\Actions\CreatePlant;

use Illuminate\Http\UploadedFile;

final readonly class CreatePlantInput
{
    /** @param array<string, mixed> $attributes colonnes de product_plants */
    public function __construct(
        public array $attributes,
        public ?UploadedFile $image = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            attributes: collect($data)->only(['nom', 'genre'])->all(),
            image: $data['image'] ?? null,
        );
    }
}
