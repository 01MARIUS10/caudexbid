<?php

namespace App\Modules\Message\Actions\CountUnreadMessages;

use App\Modules\Message\Repository\MessageRepository;

/**
 * Porte d'entrée du module Message : nombre de messages non lus (tableau de bord).
 */
final class CountUnreadMessagesAction
{
    public function __construct(
        private readonly CountUnreadMessagesValidation $validation,
        private readonly MessageRepository $messages,
    ) {}

    public function execute(CountUnreadMessagesInput $input): CountUnreadMessagesOutput
    {
        $this->validation->validate($input);

        return new CountUnreadMessagesOutput($this->messages->unreadCountFor($input->destinataireId));
    }
}
