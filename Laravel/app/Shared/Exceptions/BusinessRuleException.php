<?php

namespace App\Shared\Exceptions;

use RuntimeException;

/**
 * Violation d'une règle métier : rendue en JSON (422 par défaut, 403 pour un droit manquant).
 */
class BusinessRuleException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status = 422)
    {
        parent::__construct($message);
    }

    public static function forbidden(string $message = 'Action non autorisée pour votre grade.'): self
    {
        return new self($message, 403);
    }
}
