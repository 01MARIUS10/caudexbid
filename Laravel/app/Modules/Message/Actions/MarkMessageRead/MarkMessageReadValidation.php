<?php

namespace App\Modules\Message\Actions\MarkMessageRead;

use App\Modules\Message\Repository\MessageRepository;
use App\Shared\Exceptions\BusinessRuleException;

final class MarkMessageReadValidation
{
    public function __construct(private readonly MessageRepository $messages) {}

    /** Seul le destinataire peut marquer un message comme lu. */
    public function validate(MarkMessageReadInput $input): void
    {
        if ($this->messages->find($input->messageId)->destinataire_id !== $input->readerId) {
            throw BusinessRuleException::forbidden('Ce message ne vous est pas destiné.');
        }
    }
}
