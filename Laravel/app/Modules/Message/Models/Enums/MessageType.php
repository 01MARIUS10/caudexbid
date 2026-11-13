<?php

namespace App\Modules\Message\Models\Enums;

enum MessageType: string
{
    case General = 'general';
    /** Demande de statut de membre régulier (adressée à un premium). */
    case RegularMemberRequest = 'regular_member_request';
    /** Enregistrement. */
    case Registration = 'registration';
}
