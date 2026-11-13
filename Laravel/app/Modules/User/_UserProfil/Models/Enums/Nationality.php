<?php

namespace App\Modules\User\_UserProfil\Models\Enums;

/**
 * Nationalité saisie à l'inscription : Chine / Japon / Corée / Madagascar / Autre.
 */
enum Nationality: string
{
    case China = 'china';
    case Japan = 'japan';
    case Korea = 'korea';
    case Madagascar = 'madagascar';
    case Other = 'other';
}
