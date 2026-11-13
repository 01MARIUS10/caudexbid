<?php

namespace App\Modules\User\_UserAuth\Actions\ListMembers;

use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use Illuminate\Support\Collection;

/**
 * Porte d'entrée du module User : liste de membres (grade + profil + parrain chargés).
 */
final class ListMembersAction
{
    public function __construct(
        private readonly ListMembersValidation $validation,
        private readonly UserAuthRepository $users,
    ) {}

    public function execute(ListMembersInput $input): Collection
    {
        $this->validation->validate($input);

        return $this->users->list($input->ids, $input->identifiants, $input->grades);
    }
}
