<?php

namespace App\Modules\User\_UserGrad\Repository;

use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Modules\User\_UserGrad\Models\Grade;
use Illuminate\Support\Collection;

class GradeRepository
{
    public function all(): Collection
    {
        return Grade::orderBy('niveau')->get();
    }

    public function findByCode(GradeCode $code): Grade
    {
        return Grade::where('code', $code->value)->firstOrFail();
    }
}
