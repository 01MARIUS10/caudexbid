<?php

namespace App\Modules\User\_UserAuth\Actions\WebLogin;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserAuth\Repository\UserAuthRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Connexion web (session), ouverte à tous les membres.
 * L'accès au back office est contrôlé route par route (EnsureGrade), pas ici.
 */
final class WebLoginAction
{
    public function __construct(
        private readonly WebLoginValidation $validation,
        private readonly UserAuthRepository $users,
    ) {}

    public function execute(WebLoginInput $input): UserAuth
    {
        $this->validation->validate($input);

        $user = $this->users->findByIdentifiant($input->identifiant);
        Auth::guard('web')->login($user, $input->remember);
        $this->users->touchLastLogin($user);

        return $user;
    }
}
