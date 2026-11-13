<?php

namespace App\_Api\Document;

use App\Http\Controllers\Controller;
use App\Modules\Product\Repository\DocumentRepository;
use App\Modules\Product\Repository\ProductAuctionSessionRepository;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersInput;
use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Modules\User\_UserProfil\Repository\UserProfileRepository;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Documents à produire (écran + impression A4 paysage), par session.
 *
 * Autorisations :   G     M                  P                         A
 * 1 Lots            oui   oui                oui                       oui
 * 2 Adjudications   -     propres données    membres parrainés         oui
 * 3 En cours        -     propres données    membres parrainés         oui
 * 4 Répartition     -     -                  membres parrainés         oui
 * 5 Par premium     -     -                  membres parrainés         oui
 */
class DocumentController extends Controller
{
    private const TITLES = [
        1 => 'Liste des lots par session',
        2 => 'Résultat des adjudications par session',
        3 => 'Enchères en cours par session',
        4 => 'Répartition par session',
        5 => 'Adjudications par membre premium',
    ];

    public function show(
        Request $request,
        int $numero,
        DocumentRepository $documents,
        ProductAuctionSessionRepository $sessions,
        UserProfileRepository $profiles,
        ListMembersAction $listMembers,
    ): JsonResponse {
        if (! isset(self::TITLES[$numero])) {
            throw new BusinessRuleException('Document inconnu.', 404);
        }

        $session = $request->filled('session_id')
            ? $sessions->find($request->integer('session_id'))
            : $sessions->current();
        if (! $session) {
            throw new BusinessRuleException('Session introuvable.', 404);
        }

        $scope = $this->scope($numero, $request->user(), $profiles);
        $members = $listMembers->execute(new ListMembersInput)->keyBy('id');

        $rows = match ($numero) {
            1 => $documents->lots($session->id),
            2 => $documents->adjudications($session->id, $scope, $members),
            3 => $documents->enchereEnCours($session->id, $scope, $members),
            4 => $documents->repartition($session->id, $scope),
            5 => $documents->parPremium($session->id, $scope, $members),
        };

        return response()->json([
            'document' => $numero,
            'titre' => self::TITLES[$numero],
            'session' => ['id' => $session->id, 'titre' => $session->titre],
            'format' => 'A4 paysage',
            'lignes' => $rows,
        ]);
    }

    /** @return list<int>|null null = toutes les données */
    private function scope(int $numero, UserAuth $user, UserProfileRepository $profiles): ?array
    {
        if ($numero === 1 || $user->isAdmin()) {
            return null;
        }

        return match (true) {
            $user->hasGrade(GradeCode::Premium) => $profiles->sponsoredMemberIds($user->id),
            $user->hasGrade(GradeCode::Regulier) && in_array($numero, [2, 3], true) => [$user->id],
            default => throw BusinessRuleException::forbidden('Ce document n\'est pas accessible à votre grade.'),
        };
    }
}
