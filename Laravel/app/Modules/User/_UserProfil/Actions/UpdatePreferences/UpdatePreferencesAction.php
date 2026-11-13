<?php

namespace App\Modules\User\_UserProfil\Actions\UpdatePreferences;

use App\Modules\User\_UserProfil\Models\UserPreference;
use App\Modules\User\_UserProfil\Repository\UserPreferenceRepository;

final class UpdatePreferencesAction
{
    public function __construct(
        private readonly UpdatePreferencesValidation $validation,
        private readonly UserPreferenceRepository $preferences,
    ) {}

    public function execute(UpdatePreferencesInput $input): UserPreference
    {
        $this->validation->validate($input);

        $preference = $this->preferences->findByMembre($input->membreId)
            ?? $this->preferences->create(['membre_id' => $input->membreId]);

        return $this->preferences->update($preference, $input->attributes);
    }
}
