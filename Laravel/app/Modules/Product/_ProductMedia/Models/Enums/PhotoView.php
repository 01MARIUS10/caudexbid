<?php

namespace App\Modules\Product\_ProductMedia\Models\Enums;

/**
 * Politique des médias : 1 vue de face, 2 vues de côté, 1 vue arrière.
 */
enum PhotoView: string
{
    case Front = 'front';
    case Side = 'side';
    case Back = 'back';
}
