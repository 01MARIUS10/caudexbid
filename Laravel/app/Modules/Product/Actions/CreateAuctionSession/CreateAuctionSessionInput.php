<?php

namespace App\Modules\Product\Actions\CreateAuctionSession;

use Carbon\CarbonImmutable;

final readonly class CreateAuctionSessionInput
{
    public function __construct(
        public ?string $titre,
        public CarbonImmutable $debutLe,
        public CarbonImmutable $clotureLe,
    ) {}

    public static function fromArray(array $data): self
    {
        $tz = config('caudexbid.display_timezone');

        return new self(
            titre: $data['titre'] ?? null,
            // Les dates sont saisies en heure coréenne (KST) et stockées en UTC.
            debutLe: CarbonImmutable::parse($data['debut_le'], $tz)->utc(),
            clotureLe: CarbonImmutable::parse($data['cloture_le'], $tz)->utc(),
        );
    }
}
