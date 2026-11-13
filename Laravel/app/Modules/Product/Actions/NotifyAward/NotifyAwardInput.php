<?php

namespace App\Modules\Product\Actions\NotifyAward;

final readonly class NotifyAwardInput
{
    public function __construct(public int $produitId) {}

    public static function fromArray(array $data): self
    {
        return new self((int) $data['produit_id']);
    }
}
