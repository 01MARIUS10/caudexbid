<?php

namespace App\Modules\User\_UserProfil\Models;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserProfil\Models\Enums\Nationality;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Informations personnelles + parrain (1-1 avec user_auths).
 *
 * @property int $membre_id
 * @property int|null $parrain_id
 */
class UserProfile extends Model
{
    protected $table = 'user_profiles';

    protected $fillable = ['membre_id', 'nom', 'telephone', 'adresse', 'pays', 'nationalite', 'parrain_id'];

    protected function casts(): array
    {
        return [
            'nationalite' => Nationality::class,
        ];
    }

    public function membre(): BelongsTo
    {
        return $this->belongsTo(UserAuth::class, 'membre_id');
    }

    public function parrain(): BelongsTo
    {
        return $this->belongsTo(UserAuth::class, 'parrain_id');
    }
}
