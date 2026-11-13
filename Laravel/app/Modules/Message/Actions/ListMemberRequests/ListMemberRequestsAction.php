<?php

namespace App\Modules\Message\Actions\ListMemberRequests;

use App\Modules\Message\Repository\MessageRepository;
use Illuminate\Support\Collection;

/**
 * Porte d'entrée du module Message : demandes de statut de membre régulier reçues par un premium.
 */
final class ListMemberRequestsAction
{
    public function __construct(
        private readonly ListMemberRequestsValidation $validation,
        private readonly MessageRepository $messages,
    ) {}

    public function execute(ListMemberRequestsInput $input): Collection
    {
        $this->validation->validate($input);

        return $this->messages->memberRequests($input->premiumId, $input->requesterId);
    }
}
