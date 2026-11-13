<?php

namespace App\Modules\User\_UserAuth\ViewModels;

use App\Modules\User\_UserGrad\ViewModels\GradeViewModel;
use App\Modules\User\_UserProfil\ViewModels\UserPreferenceViewModel;
use App\Modules\User\_UserProfil\ViewModels\UserProfileViewModel;
use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\User\_UserAuth\Models\UserAuth */
class UserAuthViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'id' => $this->id,
            'identifiant' => $this->identifiant,
            'email' => $this->email,
            'en_tete' => $this->headerLabel(),
            'grade' => $this->grade() ? new GradeViewModel($this->grade()) : null,
            'derniere_connexion_le' => $this->derniere_connexion_le,
            'inscrit_le' => $this->created_at,
            'profil' => $this->whenLoaded('profile', fn () => $this->profile ? new UserProfileViewModel($this->profile) : null),
            'preferences' => $this->whenLoaded('preference', fn () => $this->preference ? new UserPreferenceViewModel($this->preference) : null),
        ];
    }
}
