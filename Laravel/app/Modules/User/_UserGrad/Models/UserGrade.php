<?php

namespace App\Modules\User\_UserGrad\Models;

use App\Modules\User\_UserAuth\Models\UserAuth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Attribution d'un grade à un membre (historique).
 *
 * @property int $id
 * @property int $user_id
 * @property int $grade_id
 * @property int|null $edited_by_user_id
 */
class UserGrade extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'user_grades';

    protected $fillable = ['user_id', 'grade_id', 'edited_by_user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserAuth::class, 'user_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(UserAuth::class, 'edited_by_user_id');
    }
}
