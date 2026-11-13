<?php

namespace App\_Backoffice\Member\ViewModels;

use App\Shared\ViewModels\ViewModel;

/** Ligne de l'historique des grades d'un membre. @mixin \App\Modules\User\_UserGrad\Models\UserGrade */
class UserGradeBoViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'grade_code' => $this->grade->code,
            'grade_nom' => $this->grade->nom,
            'edited_by' => $this->editedBy?->identifiant,
            'created_at' => $this->created_at,
        ];
    }
}
