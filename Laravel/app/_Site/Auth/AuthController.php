<?php

namespace App\_Site\Auth;

use App\Http\Controllers\Controller;
use App\Modules\User\_UserAuth\Actions\WebLogin\WebLoginAction;
use App\Modules\User\_UserAuth\Actions\WebLogin\WebLoginInput;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Connexion commune à tous les membres (/login).
 * Pas de redirection vers le back office : l'administrateur y accède depuis la navigation.
 */
class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth::login');
    }

    public function login(Request $request, WebLoginAction $action): RedirectResponse
    {
        $action->execute(WebLoginInput::fromArray($request->all()));
        $request->session()->regenerate();

        return redirect()->intended(route('site.home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('site.home');
    }
}
