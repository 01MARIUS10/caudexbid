<?php

namespace App\Modules\User\_UserProfil\Repository;

use App\Modules\User\_UserProfil\Models\UserPreference;

class UserPreferenceRepository
{
    public function findByMembre(int $membreId): ?UserPreference
    {
        return UserPreference::where('membre_id', $membreId)->first();
    }

    public function create(array $attributes): UserPreference
    {
        return UserPreference::create($attributes);
    }

    public function update(UserPreference $preference, array $attributes): UserPreference
    {
        $preference->fill($attributes)->save();

        return $preference->refresh();
    }
}
