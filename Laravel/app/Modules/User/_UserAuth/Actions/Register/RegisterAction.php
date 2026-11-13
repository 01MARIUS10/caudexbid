<?php

namespace App\Modules\User\_UserAuth\Actions\Register;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Modules\User\_UserGrad\Repository\GradeRepository;
use App\Modules\User\_UserGrad\Repository\UserGradeRepository;
use App\Modules\User\_UserProfil\Repository\UserPreferenceRepository;
use App\Modules\User\_UserProfil\Repository\UserProfileRepository;
use Illuminate\Support\Facades\DB;

/**
 * L'inscription crée un compte de grade général (G) : user_auths + profil + préférences + 1re ligne user_grades.
 */
final class RegisterAction
{
    public function __construct(
        private readonly RegisterValidation $validation,
        private readonly UserAuthRepository $users,
        private readonly UserProfileRepository $profiles,
        private readonly UserPreferenceRepository $preferences,
        private readonly GradeRepository $grades,
        private readonly UserGradeRepository $userGrades,
    ) {}

    public function execute(RegisterInput $input): UserAuth
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
                'parrain_id' => $this->users->findByIdentifiant($input->parrainIdentifiant)->id,
            ]);

            $this->preferences->create(['membre_id' => $user->id]);
            $this->userGrades->assign($user->id, $this->grades->findByCode(GradeCode::General)->id, null);

            return $this->users->findById($user->id);
        });
    }
}
