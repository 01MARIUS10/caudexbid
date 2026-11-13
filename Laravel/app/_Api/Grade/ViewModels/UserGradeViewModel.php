<?php

namespace App\_Api\Grade\ViewModels;

use App\Modules\User\_UserGrad\ViewModels\GradeViewModel;
use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\User\_UserGrad\Models\UserGrade */
class UserGradeViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'grade' => $this->whenLoaded('grade', fn () => new GradeViewModel($this->grade)),
            'edited_by' => $this->editedBy?->identifiant,
            'created_at' => $this->created_at,
        ];
    }
}
