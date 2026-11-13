<?php

namespace App\Modules\Product\_ProductBid\Actions\PlaceBid;

use App\Modules\User\_UserAuth\Models\UserAuth;

/**
 * Écran d'enchère : montant (par pas d'enchère) + case « Proxy bid » et son plafond.
 */
final readonly class PlaceBidInput
{
    public function __construct(
        public UserAuth $membre,
        public int $produitId,
        public string $montant,
        public bool $proxy,
        public ?string $plafond,
    ) {}

    public static function fromArray(array $data, UserAuth $membre): self
    {
        $proxy = (bool) ($data['proxy'] ?? false);

        return new self(
            membre: $membre,
            produitId: (int) $data['produit_id'],
            montant: (string) $data['montant'],
            proxy: $proxy,
            plafond: $proxy ? (string) $data['plafond'] : null,
        );
    }
}
