<?php

namespace App\Modules\Product\Actions\SettleProduct;

final readonly class SettleProductInput
{
    public function __construct(public int $produitId) {}

    public static function fromArray(array $data): self
    {
        return new self((int) $data['produit_id']);
    }
}
