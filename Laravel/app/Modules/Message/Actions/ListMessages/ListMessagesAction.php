<?php

namespace App\Modules\Message\Actions\ListMessages;

use App\Modules\Message\Repository\MessageRepository;
use Illuminate\Support\Collection;

/**
 * Porte d'entrée du module Message : derniers messages envoyés ou reçus par un membre.
 */
final class ListMessagesAction
{
    public function __construct(
        private readonly ListMessagesValidation $validation,
        private readonly MessageRepository $messages,
    ) {}

    public function execute(ListMessagesInput $input): Collection
    {
        $this->validation->validate($input);

        return $this->messages->latest($input->type, $input->membreId, $input->limit);
    }
}
