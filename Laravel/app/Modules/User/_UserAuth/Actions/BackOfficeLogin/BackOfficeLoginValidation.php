<?php

namespace App\Modules\User\_UserAuth\Actions\BackOfficeLogin;

use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Hash;

final class BackOfficeLoginValidation
{
    public function __construct(private readonly UserAuthRepository $users) {}

    public function validate(BackOfficeLoginInput $input): void
    {
        $user = $this->users->findByIdentifiant($input->identifiant);

        if (! $user || ! Hash::check($input->password, $user->password)) {
            throw new BusinessRuleException('Identifiant ou mot de passe incorrect.', 401);
        }

        if (! $user->hasGrade(...BackOfficeLoginAction::ALLOWED_GRADES)) {
            throw BusinessRuleException::forbidden('Le back office est réservé à l\'administrateur.');
        }
    }
}
