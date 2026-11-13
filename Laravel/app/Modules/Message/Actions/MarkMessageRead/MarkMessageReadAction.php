<?php

namespace App\Modules\Message\Actions\MarkMessageRead;

use App\Modules\Message\Models\Message;
use App\Modules\Message\Repository\MessageRepository;

final class MarkMessageReadAction
{
    public function __construct(
        private readonly MarkMessageReadValidation $validation,
        private readonly MessageRepository $messages,
    ) {}

    public function execute(MarkMessageReadInput $input): Message
    {
        $this->validation->validate($input);

        return $this->messages->markRead($this->messages->find($input->messageId));
    }
}
