<?php

namespace App\Modules\User\_UserGrad\Actions\CountMembersByGrade;

final readonly class CountMembersByGradeOutput
{
    /** @param array<string, int> $counts nombre de membres par code de grade (G, M, P, A) */
    public function __construct(public array $counts) {}

    public function total(): int
    {
        return array_sum($this->counts);
    }
}
