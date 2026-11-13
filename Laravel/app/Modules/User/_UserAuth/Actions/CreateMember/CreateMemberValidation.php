<?php

namespace App\Modules\User\_UserAuth\Actions\CreateMember;

use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Shared\Exceptions\BusinessRuleException;

final class CreateMemberValidation
{
    public function __construct(private readonly UserAuthRepository $users) {}

    public function validate(CreateMemberInput $input): void
    {
        if (! $input->admin->isAdmin()) {
            throw BusinessRuleException::forbidden();
        }

        if ($input->parrainId !== null && ! $this->users->isPremium($input->parrainId)) {
            throw new BusinessRuleException('Le parrain doit être un membre premium.');
        }
    }
}
