<?php

namespace App\Modules\User\_UserAuth\ViewModels\BackOffice;

use App\Shared\ViewModels\ViewModel;

/**
 * Membre pour le back office : compte + profil + préférences à plat. Lu par les vues via toArray().
 *
 * @mixin \App\Modules\User\_UserAuth\Models\UserAuth
 */
class MemberBoViewModel extends ViewModel
{
    protected function present(): array
    {
        $profile = $this->profile;
        $pref = $this->preference;

        return [
            'id' => $this->id,
            'identifiant' => $this->identifiant,
            'email' => $this->email,
            'grade_code' => $this->gradeCode()->value,
            'created_at' => $this->created_at,
            'derniere_connexion_le' => $this->derniere_connexion_le,
            'nom' => $profile?->nom,
            'telephone' => $profile?->telephone,
            'adresse' => $profile?->adresse,
            'pays' => $profile?->pays,
            'nationalite' => $profile?->nationalite?->value,
            'parrain_id' => $profile?->parrain?->id,
            'parrain' => $profile?->parrain?->identifiant,
            'fuseau_horaire' => $pref?->fuseau_horaire,
            'notif_surenchere' => (bool) $pref?->notif_surenchere,
            'notif_adjudication' => (bool) $pref?->notif_adjudication,
            'notif_nouveau_message' => (bool) $pref?->notif_nouveau_message,
            'notif_prochaine_session' => (bool) $pref?->notif_prochaine_session,
        ];
    }
}
