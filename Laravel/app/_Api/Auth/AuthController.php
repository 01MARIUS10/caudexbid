<?php

namespace App\_Api\Auth;

use App\Http\Controllers\Controller;
use App\Modules\User\_UserAuth\Actions\Login\LoginAction;
use App\Modules\User\_UserAuth\Actions\Login\LoginInput;
use App\Modules\User\_UserAuth\Actions\Logout\LogoutAction;
use App\Modules\User\_UserAuth\Actions\Logout\LogoutInput;
use App\Modules\User\_UserAuth\Actions\Register\RegisterAction;
use App\Modules\User\_UserAuth\Actions\Register\RegisterInput;
use App\Modules\User\_UserAuth\ViewModels\UserAuthViewModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function register(Request $request, RegisterAction $action): UserAuthViewModel
    {
        $user = $action->execute(RegisterInput::fromArray($request->all()));

        return (new UserAuthViewModel($user->load('profile.parrain', 'preference')))->created();
    }

    public function login(Request $request, LoginAction $action): JsonResponse
    {
        $result = $action->execute(LoginInput::fromArray($request->all()));

        return response()->json([
            'token' => $result->token,
            'user' => new UserAuthViewModel($result->user->load('currentUserGrade.grade', 'profile.parrain', 'preference')),
        ]);
    }

    public function logout(Request $request, LogoutAction $action): Response
    {
        $action->execute(LogoutInput::fromArray($request->all(), $request->user()));

        return response()->noContent();
    }

    /** En-tête après connexion : identifiant + grade. */
    public function me(Request $request): UserAuthViewModel
    {
        return new UserAuthViewModel($request->user()->load('currentUserGrade.grade', 'profile.parrain', 'preference'));
    }
}
