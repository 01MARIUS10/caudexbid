<?php

namespace App\_Backoffice\Member\ViewModels;

use App\Shared\ViewModels\ViewModel;

/** Filleul d'un membre premium (profil + identifiant du membre). @mixin \App\Modules\User\_UserProfil\Models\UserProfile */
class SponsoredMemberBoViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'id' => $this->membre_id,
            'identifiant' => $this->membre?->identifiant,
        ];
    }
}
