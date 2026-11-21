<?php

namespace App\Modules\User\_UserAuth\Actions\WebLogin;

use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Hash;

final class WebLoginValidation
{
    public function __construct(private readonly UserAuthRepository $users) {}

    public function validate(WebLoginInput $input): void
    {
        $user = $this->users->findByIdentifiant($input->identifiant);

        if (! $user || ! Hash::check($input->password, $user->password)) {
            throw new BusinessRuleException('Identifiant ou mot de passe incorrect.', 401);
        }
    }
}
