<?php

namespace App\Modules\User\_UserGrad\Actions\ChangeGrade;

use App\Modules\User\_UserGrad\Models\UserGrade;
use App\Modules\User\_UserGrad\Repository\GradeRepository;
use App\Modules\User\_UserGrad\Repository\UserGradeRepository;
use App\Modules\User\_UserProfil\Repository\UserProfileRepository;
use Illuminate\Support\Facades\DB;

/**
 * Attribue un nouveau grade. Un premium qui valide la demande d'un membre sans parrain devient son parrain.
 */
final class ChangeGradeAction
{
    public function __construct(
        private readonly ChangeGradeValidation $validation,
        private readonly UserProfileRepository $profiles,
        private readonly GradeRepository $grades,
        private readonly UserGradeRepository $userGrades,
    ) {}

    public function execute(ChangeGradeInput $input): UserGrade
    {
        $this->validation->validate($input);

        return DB::transaction(function () use ($input) {
            if ($input->actor->isPremium() && $this->profiles->findByMembre($input->targetUserId)?->parrain_id === null) {
                $this->profiles->setParrain($input->targetUserId, $input->actor->id);
            }

            return $this->userGrades->assign(
                $input->targetUserId,
                $this->grades->findByCode($input->newGrade)->id,
                $input->actor->id,
            );
        });
    }
}
