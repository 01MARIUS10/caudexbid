<?php

namespace App\Modules\Product\_ProductAchat\Actions\BuyNow;

use App\Modules\User\_UserAuth\Models\UserAuth;

final readonly class BuyNowInput
{
    public function __construct(
        public UserAuth $acheteur,
        public int $produitId,
    ) {}

    public static function fromArray(array $data, UserAuth $acheteur): self
    {
        return new self($acheteur, (int) $data['produit_id']);
    }
}
