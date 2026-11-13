<?php

namespace App\_Api\Profil;

use App\Http\Controllers\Controller;
use App\Modules\User\_UserProfil\Actions\UpdatePreferences\UpdatePreferencesAction;
use App\Modules\User\_UserProfil\Actions\UpdatePreferences\UpdatePreferencesInput;
use App\Modules\User\_UserProfil\Actions\UpdateProfil\UpdateProfilAction;
use App\Modules\User\_UserProfil\Actions\UpdateProfil\UpdateProfilInput;
use App\Modules\User\_UserProfil\ViewModels\UserPreferenceViewModel;
use App\Modules\User\_UserProfil\ViewModels\UserProfileViewModel;
use App\Modules\User\_UserProfil\Repository\UserPreferenceRepository;
use App\Modules\User\_UserProfil\Repository\UserProfileRepository;
use Illuminate\Http\Request;

/**
 * Menu « Modification des informations personnelles » + préférences.
 */
class ProfilController extends Controller
{
    public function show(Request $request, UserProfileRepository $profiles): UserProfileViewModel
    {
        return new UserProfileViewModel($profiles->findByMembre($request->user()->id));
    }

    public function update(Request $request, UpdateProfilAction $action): UserProfileViewModel
    {
        $profile = $action->execute(UpdateProfilInput::fromArray($request->all(), $request->user()->id));

        return new UserProfileViewModel($profile->load('parrain'));
    }

    public function showPreferences(Request $request, UserPreferenceRepository $preferences): UserPreferenceViewModel
    {
        return new UserPreferenceViewModel($preferences->findByMembre($request->user()->id));
    }

    public function updatePreferences(Request $request, UpdatePreferencesAction $action): UserPreferenceViewModel
    {
        return new UserPreferenceViewModel(
            $action->execute(UpdatePreferencesInput::fromArray($request->all(), $request->user()->id))
        );
    }
}
