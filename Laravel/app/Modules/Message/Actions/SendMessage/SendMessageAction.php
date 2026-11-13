<?php

namespace App\Modules\Message\Actions\SendMessage;

use App\Modules\Message\Models\Message;
use App\Modules\Message\Repository\MessageRepository;

final class SendMessageAction
{
    public function __construct(
        private readonly SendMessageValidation $validation,
        private readonly MessageRepository $messages,
    ) {}

    public function execute(SendMessageInput $input): Message
    {
        $this->validation->validate($input);

        return $this->messages->create([
            'expediteur_id' => $input->expediteur->id,
            'destinataire_id' => $input->destinataireId,
            'type' => $input->type,
            'memo' => $input->memo,
        ]);
    }
}
