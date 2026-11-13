<?php

namespace App\Modules\User\_UserGrad\ViewModels;

use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\User\_UserGrad\Models\Grade */
class GradeViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'nom' => $this->nom,
            'niveau' => $this->niveau,
            'peut_encherir' => $this->peut_encherir,
            'peut_changer_grade' => $this->peut_changer_grade,
        ];
    }
}
