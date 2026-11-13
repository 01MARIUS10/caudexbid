<?php

namespace App\Modules\Product\_ProductAchat\Actions\DefineBuyNowPrice;

final readonly class DefineBuyNowPriceInput
{
    public function __construct(
        public int $produitId,
        public string $prix,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self((int) $data['produit_id'], (string) $data['prix']);
    }
}
