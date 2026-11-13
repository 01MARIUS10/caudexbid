<?php

namespace App\Modules\User\_UserAuth\Actions\CreateMember;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Modules\User\_UserGrad\Repository\GradeRepository;
use App\Modules\User\_UserGrad\Repository\UserGradeRepository;
use App\Modules\User\_UserProfil\Repository\UserPreferenceRepository;
use App\Modules\User\_UserProfil\Repository\UserProfileRepository;
use Illuminate\Support\Facades\DB;

final class CreateMemberAction
{
    public function __construct(
        private readonly CreateMemberValidation $validation,
        private readonly UserAuthRepository $users,
        private readonly UserProfileRepository $profiles,
        private readonly UserPreferenceRepository $preferences,
        private readonly GradeRepository $grades,
        private readonly UserGradeRepository $userGrades,
    ) {}

    public function execute(CreateMemberInput $input): UserAuth
    {
        $this->validation->validate($input);

        return DB::transaction(function () use ($input) {
            $user = $this->users->create([
                'identifiant' => $input->identifiant,
                'email' => $input->email,
                'password' => $input->password,
            ]);

            $this->profiles->create([
                'membre_id' => $user->id,
                'nom' => $input->nom,
                'telephone' => $input->telephone,
                'adresse' => $input->adresse,
                'pays' => $input->pays,
                'nationalite' => $input->nationalite,
                'parrain_id' => $input->parrainId,
            ]);

            $this->preferences->create(['membre_id' => $user->id]);
            $this->userGrades->assign($user->id, $this->grades->findByCode($input->grade)->id, $input->admin->id);

            return $this->users->findById($user->id);
        });
    }
}
