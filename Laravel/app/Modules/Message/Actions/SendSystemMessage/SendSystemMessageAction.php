<?php

namespace App\Modules\Message\Actions\SendSystemMessage;

use App\Modules\Message\Models\Enums\MessageType;
use App\Modules\Message\Models\Message;
use App\Modules\Message\Repository\MessageRepository;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersInput;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Shared\Exceptions\BusinessRuleException;

/**
 * Porte d'entrée du module Message : envoie un message au nom du compte système
 * (config caudexbid.system_sender_identifiant, sinon le premier administrateur).
 */
final class SendSystemMessageAction
{
    public function __construct(
        private readonly SendSystemMessageValidation $validation,
        private readonly MessageRepository $messages,
        private readonly ListMembersAction $listMembers,
    ) {}

    public function execute(SendSystemMessageInput $input): Message
    {
        $this->validation->validate($input);

        $sender = $this->listMembers->execute(new ListMembersInput(identifiants: [config('caudexbid.system_sender_identifiant')]))->first()
            ?? $this->listMembers->execute(new ListMembersInput(grades: [GradeCode::Admin]))->first()
            ?? throw new BusinessRuleException('Aucun compte administrateur pour envoyer le message système.');

        return $this->messages->create([
            'expediteur_id' => $sender->id,
            'destinataire_id' => $input->destinataireId,
            'type' => MessageType::General,
            'memo' => $input->memo,
        ]);
    }
}
