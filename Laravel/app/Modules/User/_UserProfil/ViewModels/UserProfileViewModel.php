<?php

namespace App\Modules\User\_UserProfil\ViewModels;

use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\User\_UserProfil\Models\UserProfile */
class UserProfileViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'pays' => $this->pays,
            'nationalite' => $this->nationalite?->value,
            'parrain' => $this->parrain?->identifiant,
        ];
    }
}
