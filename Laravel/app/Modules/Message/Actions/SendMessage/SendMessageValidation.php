<?php

namespace App\Modules\Message\Actions\SendMessage;

use App\Modules\Message\Models\Enums\MessageType;
use App\Modules\User\_UserAuth\Actions\FindMember\FindMemberAction;
use App\Modules\User\_UserAuth\Actions\FindMember\FindMemberInput;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Shared\Exceptions\BusinessRuleException;

/**
 * - un membre général (G) écrit uniquement aux membres premium ;
 * - une demande de statut de membre régulier va d'un G vers un premium.
 */
final class SendMessageValidation
{
    public function __construct(private readonly FindMemberAction $findMember) {}

    public function validate(SendMessageInput $input): void
    {
        $toPremium = $this->findMember->execute(new FindMemberInput($input->destinataireId))->isPremium();
        $fromGeneral = $input->expediteur->hasGrade(GradeCode::General);

        if ($fromGeneral && ! $toPremium) {
            throw BusinessRuleException::forbidden('Un membre général ne peut écrire qu\'aux membres premium.');
        }

        if ($input->type === MessageType::RegularMemberRequest) {
            if (! $toPremium) {
                throw new BusinessRuleException('La demande de statut doit être adressée à un membre premium.');
            }
            if (! $fromGeneral) {
                throw new BusinessRuleException('Seul un membre général peut demander le statut de membre régulier.');
            }
        }
    }
}
