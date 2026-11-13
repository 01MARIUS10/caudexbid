<?php

namespace App\Modules\Product\Actions\CloseExpiredAuctions;

use Carbon\CarbonImmutable;

final readonly class CloseExpiredAuctionsInput
{
    public function __construct(public CarbonImmutable $now) {}

    public static function fromArray(array $data): self
    {
        return new self(isset($data['now']) ? CarbonImmutable::parse($data['now']) : CarbonImmutable::now());
    }
}
