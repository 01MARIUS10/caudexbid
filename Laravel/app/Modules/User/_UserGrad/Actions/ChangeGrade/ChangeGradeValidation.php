<?php

namespace App\Modules\User\_UserGrad\Actions\ChangeGrade;

use App\Modules\Message\Actions\ListMemberRequests\ListMemberRequestsAction;
use App\Modules\Message\Actions\ListMemberRequests\ListMemberRequestsInput;
use App\Modules\Product\Actions\ListUnsettledAwards\ListUnsettledAwardsAction;
use App\Modules\Product\Actions\ListUnsettledAwards\ListUnsettledAwardsInput;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Modules\User\_UserProfil\Repository\UserProfileRepository;
use App\Shared\Exceptions\BusinessRuleException;

/**
 * Règles (cahier des charges §6) :
 * - seuls P et A peuvent changer un grade ; jamais le sien ;
 * - premium : G <-> M uniquement ;
 *     - membre sans parrain : il doit avoir adressé une demande au premium (module Message) ;
 *     - membre avec parrain : seul ce parrain peut changer son grade ;
 * - M -> G impossible tant qu'une adjudication du membre n'est pas soldée (module Product).
 */
final class ChangeGradeValidation
{
    public function __construct(
        private readonly UserAuthRepository $users,
        private readonly UserProfileRepository $profiles,
        private readonly ListMemberRequestsAction $listMemberRequests,
        private readonly ListUnsettledAwardsAction $listUnsettledAwards,
    ) {}

    public function validate(ChangeGradeInput $input): void
    {
        $actor = $input->actor;

        if (! $actor->grade()?->peut_changer_grade) {
            throw BusinessRuleException::forbidden('Le changement de grade est réservé aux membres premium et aux administrateurs.');
        }
        if ($input->targetUserId === $actor->id) {
            throw new BusinessRuleException('Vous ne pouvez pas modifier votre propre grade.');
        }

        $current = $this->users->findById($input->targetUserId)->gradeCode();

        if ($current === $input->newGrade) {
            throw new BusinessRuleException('Le membre a déjà ce grade.');
        }

        if ($actor->isPremium()) {
            $this->checkPremiumRights($input, $current);
        }

        if ($current === GradeCode::Regulier && $input->newGrade === GradeCode::General
            && $this->listUnsettledAwards->execute(ListUnsettledAwardsInput::fromArray(['adjudicataire_ids' => [$input->targetUserId]]))->isNotEmpty()) {
            throw new BusinessRuleException('Passage M -> G impossible : ce membre a une adjudication non soldée.');
        }
    }

    private function checkPremiumRights(ChangeGradeInput $input, GradeCode $current): void
    {
        $gm = [GradeCode::General, GradeCode::Regulier];

        if (! in_array($input->newGrade, $gm, true) || ! in_array($current, $gm, true)) {
            throw BusinessRuleException::forbidden('Un membre premium ne peut attribuer que les grades G et M.');
        }

        $parrainId = $this->profiles->findByMembre($input->targetUserId)?->parrain_id;

        if ($parrainId === null) {
            $requests = $this->listMemberRequests->execute(ListMemberRequestsInput::fromArray([
                'premium_id' => $input->actor->id,
                'requester_id' => $input->targetUserId,
            ]));

            if ($requests->isEmpty()) {
                throw BusinessRuleException::forbidden('Ce membre ne vous a pas adressé de demande de statut.');
            }
        } elseif ($parrainId !== $input->actor->id) {
            throw BusinessRuleException::forbidden('Seul le parrain de ce membre peut modifier son grade.');
        }
    }
}
