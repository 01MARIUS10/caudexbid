<?php

namespace App\_Backoffice\Grade;

use App\Modules\User\_UserGrad\ViewModels\GradeViewModel;
use App\Http\Controllers\Controller;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use App\Modules\User\_UserGrad\Actions\ChangeGrade\ChangeGradeAction;
use App\Modules\User\_UserGrad\Actions\ChangeGrade\ChangeGradeInput;
use App\Modules\User\_UserGrad\Repository\GradeRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{
    /** Référentiel des grades + nombre de membres par grade. */
    public function index(GradeRepository $grades, UserAuthRepository $users): View
    {
        return view('grade::index', [
            'grades' => GradeViewModel::collection($grades->all()),
            'counts' => $users->countByGrade(),
        ]);
    }

    public function change(Request $request, int $member, ChangeGradeAction $action): RedirectResponse
    {
        $grade = $action->execute(ChangeGradeInput::fromArray(['user_id' => $member] + $request->all(), $request->user()));

        return back()->with('status', 'Grade changé : '.$grade->grade->nom.'.');
    }
}
