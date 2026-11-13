<?php

namespace App\Modules\User\_UserProfil\Actions\ListSponsoredMembers;

use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Modules\User\_UserProfil\Repository\UserProfileRepository;
use Illuminate\Support\Collection;

/**
 * Porte d'entrée du module User : les filleuls (membres) d'un membre premium.
 */
final class ListSponsoredMembersAction
{
    public function __construct(
        private readonly ListSponsoredMembersValidation $validation,
        private readonly UserProfileRepository $profiles,
        private readonly UserAuthRepository $users,
    ) {}

    public function execute(ListSponsoredMembersInput $input): Collection
    {
        $this->validation->validate($input);

        return $this->users->list(ids: $this->profiles->sponsoredMemberIds($input->parrainId));
    }
}
