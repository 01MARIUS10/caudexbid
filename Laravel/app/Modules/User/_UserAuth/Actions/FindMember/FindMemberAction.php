<?php

namespace App\Modules\User\_UserAuth\Actions\FindMember;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;

/**
 * Porte d'entrée du module User : un membre avec son grade, son profil (+ parrain) et ses préférences.
 */
final class FindMemberAction
{
    public function __construct(
        private readonly FindMemberValidation $validation,
        private readonly UserAuthRepository $users,
    ) {}

    public function execute(FindMemberInput $input): UserAuth
    {
        $this->validation->validate($input);

        return $this->users->findById($input->membreId)->load('profile.parrain', 'preference');
    }
}
