<?php

namespace App\Modules\User\_UserProfil\Actions\UpdateProfil;

use App\Modules\User\_UserProfil\Models\UserProfile;
use App\Modules\User\_UserProfil\Repository\UserProfileRepository;

final class UpdateProfilAction
{
    public function __construct(
        private readonly UpdateProfilValidation $validation,
        private readonly UserProfileRepository $profiles,
    ) {}

    public function execute(UpdateProfilInput $input): UserProfile
    {
        $this->validation->validate($input);

        $profile = $this->profiles->findByMembre($input->membreId)
            ?? $this->profiles->create(['membre_id' => $input->membreId]);

        return $this->profiles->update($profile, $input->attributes)->load('parrain');
    }
}
