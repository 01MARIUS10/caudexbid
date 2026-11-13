<?php

namespace App\Modules\User\_UserAuth\Actions\Login;

use App\Modules\User\_UserAuth\Repository\UserAuthRepository;

/**
 * Connexion API : délivre un jeton Sanctum.
 */
final class LoginAction
{
    public function __construct(
        private readonly LoginValidation $validation,
        private readonly UserAuthRepository $users,
    ) {}

    public function execute(LoginInput $input): LoginOutput
    {
        $this->validation->validate($input);

        $user = $this->users->findByIdentifiant($input->identifiant);
        $this->users->touchLastLogin($user);

        return new LoginOutput($user, $user->createToken($input->deviceName)->plainTextToken);
    }
}
