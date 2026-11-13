<?php

namespace App\Modules\User\_UserAuth\Models;

use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Modules\User\_UserGrad\Models\Grade;
use App\Modules\User\_UserGrad\Models\UserGrade;
use App\Modules\User\_UserProfil\Models\UserPreference;
use App\Modules\User\_UserProfil\Models\UserProfile;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Membre : identité + credentials (table user_auths).
 *
 * @property int $id
 * @property string $identifiant
 * @property string $email
 */
class UserAuth extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'user_auths';

    protected $fillable = [
        'identifiant',
        'email',
        'password',
        'derniere_connexion_le',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'derniere_connexion_le' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'membre_id');
    }

    public function preference(): HasOne
    {
        return $this->hasOne(UserPreference::class, 'membre_id');
    }

    /** Historique complet des grades. */
    public function userGrades(): HasMany
    {
        return $this->hasMany(UserGrade::class, 'user_id');
    }

    /** Grade actuel = attribution la plus récente. */
    public function currentUserGrade(): HasOne
    {
        return $this->hasOne(UserGrade::class, 'user_id')->latestOfMany();
    }

    /** Profils des membres dont ce membre (premium) est le parrain. */
    public function sponsoredProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class, 'parrain_id');
    }

    public function grade(): ?Grade
    {
        return $this->currentUserGrade?->grade;
    }

    public function gradeCode(): GradeCode
    {
        return GradeCode::from($this->grade()?->code ?? GradeCode::General->value);
    }

    public function hasGrade(GradeCode ...$codes): bool
    {
        return in_array($this->gradeCode(), $codes, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasGrade(GradeCode::Admin);
    }

    public function isPremium(): bool
    {
        return $this->hasGrade(GradeCode::Premium);
    }

    /** Seuls les grades M et P peuvent enchérir. */
    public function canBid(): bool
    {
        return (bool) $this->grade()?->peut_encherir;
    }

    /** Libellé d'en-tête : « andy88 (P) ». */
    public function headerLabel(): string
    {
        return sprintf('%s (%s)', $this->identifiant, $this->gradeCode()->value);
    }
}
