<?php

namespace App\Modules\User\_UserAuth\Actions\BackOfficeLogin;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Auth;

/**
 * Connexion au back office (session web).
 * Étape 1 : réservé à l'administrateur. Ouvrir à d'autres grades en les ajoutant dans ALLOWED_GRADES
 * (et dans les combinaisons EnsureGrade::for() des routes concernées).
 */
final class BackOfficeLoginAction
{
    public const ALLOWED_GRADES = [GradeCode::Admin];

    public function __construct(
        private readonly BackOfficeLoginValidation $validation,
        private readonly UserAuthRepository $users,
    ) {}

    public function execute(BackOfficeLoginInput $input): UserAuth
    {
        $this->validation->validate($input);

        $user = $this->users->findByIdentifiant($input->identifiant);
        Auth::guard('web')->login($user, $input->remember);
        $this->users->touchLastLogin($user);

        return $user;
    }
}
