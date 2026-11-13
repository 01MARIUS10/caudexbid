<?php

namespace App\Modules\User\_UserGrad\Actions\CountMembersByGrade;

use App\Modules\User\_UserAuth\Repository\UserAuthRepository;

/**
 * Porte d'entrée du module User : nombre de membres par grade (tableau de bord).
 */
final class CountMembersByGradeAction
{
    public function __construct(
        private readonly CountMembersByGradeValidation $validation,
        private readonly UserAuthRepository $users,
    ) {}

    public function execute(CountMembersByGradeInput $input): CountMembersByGradeOutput
    {
        $this->validation->validate($input);

        return new CountMembersByGradeOutput($this->users->countByGrade());
    }
}
