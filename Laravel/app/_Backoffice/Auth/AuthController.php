<?php

namespace App\_Backoffice\Auth;

use App\Http\Controllers\Controller;
use App\Modules\User\_UserAuth\Actions\BackOfficeLogin\BackOfficeLoginAction;
use App\Modules\User\_UserAuth\Actions\BackOfficeLogin\BackOfficeLoginInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth::login');
    }

    public function login(Request $request, BackOfficeLoginAction $action): RedirectResponse
    {
        $action->execute(BackOfficeLoginInput::fromArray($request->all()));
        $request->session()->regenerate();

        return redirect()->intended(route('bo.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('bo.login');
    }
}
