<?php

namespace App\Modules\User\_UserProfil\Repository;

use App\Modules\User\_UserProfil\Models\UserProfile;

class UserProfileRepository
{
    public function findByMembre(int $membreId): ?UserProfile
    {
        return UserProfile::with('parrain')->where('membre_id', $membreId)->first();
    }

    public function create(array $attributes): UserProfile
    {
        return UserProfile::create($attributes);
    }

    public function update(UserProfile $profile, array $attributes): UserProfile
    {
        $profile->fill($attributes)->save();

        return $profile->refresh();
    }

    public function setParrain(int $membreId, int $parrainId): void
    {
        UserProfile::where('membre_id', $membreId)->update(['parrain_id' => $parrainId]);
    }

    /** Profils des filleuls d'un premium. */
    public function sponsored(int $parrainId): \Illuminate\Support\Collection
    {
        return UserProfile::with('membre.currentUserGrade.grade')->where('parrain_id', $parrainId)->get();
    }

    /** @return list<int> identifiants des membres parrainés par ce premium. */
    public function sponsoredMemberIds(int $parrainId): array
    {
        return UserProfile::where('parrain_id', $parrainId)->pluck('membre_id')->all();
    }
}
