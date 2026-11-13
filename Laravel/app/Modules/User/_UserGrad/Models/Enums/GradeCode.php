<?php

namespace App\Modules\User\_UserGrad\Models\Enums;

/**
 * Codes des grades affichés à côté de l'identifiant.
 */
enum GradeCode: string
{
    case General = 'G';
    case Regulier = 'M';
    case Premium = 'P';
    case Admin = 'A';
}
