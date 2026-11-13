<?php

namespace App\Modules\User\_UserProfil\ViewModels;

use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\User\_UserProfil\Models\UserPreference */
class UserPreferenceViewModel extends ViewModel
{
    protected function present(): array
    {
        return [
            'fuseau_horaire' => $this->fuseau_horaire,
            'notif_surenchere' => $this->notif_surenchere,
            'notif_adjudication' => $this->notif_adjudication,
            'notif_nouveau_message' => $this->notif_nouveau_message,
            'notif_prochaine_session' => $this->notif_prochaine_session,
        ];
    }
}
