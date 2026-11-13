<?php

namespace App\Modules\User\_UserAuth\Actions\Logout;

final class LogoutValidation
{
    /** Aucune règle métier : tout membre connecté peut se déconnecter. */
    public function validate(LogoutInput $input): void {}
}
