<?php

namespace App\Modules\User\_UserProfil\Models;

use App\Modules\User\_UserAuth\Models\UserAuth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Réglages de l'utilisateur (1-1 avec user_auths).
 */
class UserPreference extends Model
{
    protected $table = 'user_preferences';

    protected $fillable = [
        'membre_id',
        'fuseau_horaire',
        'notif_surenchere',
        'notif_adjudication',
        'notif_nouveau_message',
        'notif_prochaine_session',
    ];

    protected function casts(): array
    {
        return [
            'notif_surenchere' => 'boolean',
            'notif_adjudication' => 'boolean',
            'notif_nouveau_message' => 'boolean',
            'notif_prochaine_session' => 'boolean',
        ];
    }

    public function membre(): BelongsTo
    {
        return $this->belongsTo(UserAuth::class, 'membre_id');
    }
}
