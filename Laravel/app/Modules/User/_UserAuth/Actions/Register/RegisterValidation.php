<?php

namespace App\Modules\User\_UserAuth\Actions\Register;

use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Shared\Exceptions\BusinessRuleException;

final class RegisterValidation
{
    public function __construct(private readonly UserAuthRepository $users) {}

    /** Le parrain doit être un membre premium. */
    public function validate(RegisterInput $input): void
    {
        if (! $this->users->findByIdentifiant($input->parrainIdentifiant)?->isPremium()) {
            throw new BusinessRuleException('Le parrain doit être un membre premium.');
        }
    }
}
