<?php

namespace App\_Api\Grade;

use App\Http\Controllers\Controller;
use App\Modules\Message\Repository\MessageRepository;
use App\Modules\User\_UserAuth\ViewModels\UserAuthViewModel;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Modules\User\_UserGrad\Actions\ChangeGrade\ChangeGradeAction;
use App\Modules\User\_UserGrad\Actions\ChangeGrade\ChangeGradeInput;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Modules\User\_UserGrad\ViewModels\GradeViewModel;
use App\_Api\Grade\ViewModels\UserGradeViewModel;
use App\Modules\User\_UserGrad\Repository\GradeRepository;
use App\Modules\User\_UserGrad\Repository\UserGradeRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Shared\ViewModels\ViewModelCollection;

/**
 * Menu « Changement de grade » (grades P et A).
 */
class GradeController extends Controller
{
    public function index(GradeRepository $grades): ViewModelCollection
    {
        return GradeViewModel::collection($grades->all());
    }

    /**
     * Membres dont le grade peut être modifié :
     * - premium : membres qui lui ont adressé une demande + ses filleuls ;
     * - admin : tous les membres.
     */
    public function candidates(Request $request, UserAuthRepository $users, MessageRepository $messages): ViewModelCollection
    {
        $actor = $request->user();

        $members = $actor->isAdmin()
            ? $users->withGrade(...GradeCode::cases())
            : $messages->requestersFor($actor->id)
                ->merge($users->withGrade(GradeCode::General, GradeCode::Regulier)
                    ->filter(fn ($u) => $u->profile?->parrain_id === $actor->id))
                ->unique('id')
                ->values();

        return UserAuthViewModel::collection($members->load('profile.parrain'));
    }

    public function change(Request $request, int $user, ChangeGradeAction $action): UserGradeViewModel
    {
        $input = ChangeGradeInput::fromArray(['user_id' => $user] + $request->all(), $request->user());

        return (new UserGradeViewModel($action->execute($input)))->created();
    }

    public function history(int $user, UserGradeRepository $userGrades): ViewModelCollection
    {
        return UserGradeViewModel::collection($userGrades->history($user));
    }
}
