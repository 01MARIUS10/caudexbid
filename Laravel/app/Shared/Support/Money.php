<?php

namespace App\Shared\Support;

/**
 * Calculs de prix en centimes (entiers) pour éviter les erreurs d'arrondi des flottants.
 */
final class Money
{
    public static function toCents(string|int|float|null $amount): int
    {
        return (int) round(((float) ($amount ?? 0)) * 100);
    }

    public static function fromCents(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }

    /** Part (en %) d'un montant en centimes. */
    public static function percent(int $cents, int|float $percent): int
    {
        return (int) round($cents * $percent / 100);
    }
}
