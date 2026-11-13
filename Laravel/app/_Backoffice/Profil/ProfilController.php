<?php

namespace App\_Backoffice\Profil;

use App\Http\Controllers\Controller;
use App\Modules\User\_UserProfil\Actions\UpdatePreferences\UpdatePreferencesAction;
use App\Modules\User\_UserProfil\Actions\UpdatePreferences\UpdatePreferencesInput;
use App\Modules\User\_UserProfil\Actions\UpdateProfil\UpdateProfilAction;
use App\Modules\User\_UserProfil\Actions\UpdateProfil\UpdateProfilInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Modification du profil et des préférences d'un membre (depuis sa fiche).
 */
class ProfilController extends Controller
{
    public function update(Request $request, int $member, UpdateProfilAction $action): RedirectResponse
    {
        $action->execute(UpdateProfilInput::fromArray($request->except('_token', '_method'), $member));

        return back()->with('status', 'Profil mis à jour.');
    }

    public function updatePreferences(Request $request, int $member, UpdatePreferencesAction $action): RedirectResponse
    {
        // Les cases non cochées ne sont pas envoyées par le navigateur.
        $data = $request->only('fuseau_horaire') + collect([
            'notif_surenchere', 'notif_adjudication', 'notif_nouveau_message', 'notif_prochaine_session',
        ])->mapWithKeys(fn ($k) => [$k => $request->boolean($k)])->all();

        $action->execute(UpdatePreferencesInput::fromArray($data, $member));

        return back()->with('status', 'Préférences mises à jour.');
    }
}
