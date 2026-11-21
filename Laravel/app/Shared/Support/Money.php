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

    /** Devise affichée après chaque prix. */
    public const CURRENCY = '$';

    /**
     * Prix affiché : « 1 250 $ », « 12.50 $ » (décimales seulement si nécessaires), « — » si vide.
     * Espaces insécables : le montant et le symbole ne sont jamais coupés en fin de ligne.
     * À utiliser pour TOUT affichage de prix (vues : directive @money ; textes : messages, erreurs).
     */
    public static function format(string|int|float|null $amount, string $empty = '—'): string
    {
        if ($amount === null || $amount === '') {
            return $empty;
        }

        $value = round((float) $amount, 2);
        $decimals = fmod($value, 1.0) == 0.0 ? 0 : 2;

        return number_format($value, $decimals, '.', "\u{202F}")."\u{00A0}".self::CURRENCY;
    }

    /** Part (en %) d'un montant en centimes. */
    public static function percent(int $cents, int|float $percent): int
    {
        return (int) round($cents * $percent / 100);
    }
}
