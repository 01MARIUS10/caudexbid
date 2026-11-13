<?php

namespace App\Modules\Product\Models\Enums;

enum ProductStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    /** Achat immédiat confirmé -> affichage SOLD. */
    case Sold = 'sold';
    /** Adjugé à la clôture. */
    case Awarded = 'awarded';
    case Unsold = 'unsold';

    /** Vendu (adjugé ou acheté) mais pas encore réglé : le produit existe encore. */
    public function isUnsettledSale(): bool
    {
        return $this === self::Sold || $this === self::Awarded;
    }
}
