<?php

namespace App\Modules\User\_UserAuth\Actions\Logout;

use App\Modules\User\_UserAuth\Models\UserAuth;
use Laravel\Sanctum\PersonalAccessToken;

final class LogoutAction
{
    public function __construct(private readonly LogoutValidation $validation) {}

    public function execute(LogoutInput $input): UserAuth
    {
        $this->validation->validate($input);

        if ($input->allDevices) {
            $input->user->tokens()->delete();
        } elseif (($token = $input->user->currentAccessToken()) instanceof PersonalAccessToken) {
            $token->delete();
        }

        return $input->user;
    }
}
