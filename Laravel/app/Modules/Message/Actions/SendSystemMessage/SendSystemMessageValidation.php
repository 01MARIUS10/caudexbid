<?php

namespace App\Modules\Message\Actions\SendSystemMessage;

final class SendSystemMessageValidation
{
    /** Aucune règle métier : le système peut écrire à tout membre. */
    public function validate(SendSystemMessageInput $input): void {}
}
