<?php

namespace App\Modules\Product\_ProductMedia\Actions\DeleteProductMedia;

final readonly class DeleteProductMediaInput
{
    public function __construct(public int $mediaId) {}

    public static function fromArray(array $data): self
    {
        return new self((int) $data['media_id']);
    }
}
