<?php

namespace App\Modules\User\_UserGrad\Repository;

use App\Modules\User\_UserGrad\Models\UserGrade;
use Illuminate\Support\Collection;

class UserGradeRepository
{
    public function assign(int $userId, int $gradeId, ?int $editedByUserId): UserGrade
    {
        return UserGrade::create([
            'user_id' => $userId,
            'grade_id' => $gradeId,
            'edited_by_user_id' => $editedByUserId,
        ])->load('grade', 'editedBy');
    }

    public function history(int $userId): Collection
    {
        return UserGrade::with('grade', 'editedBy')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();
    }
}
