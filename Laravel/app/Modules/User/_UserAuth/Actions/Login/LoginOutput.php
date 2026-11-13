<?php

namespace App\Modules\User\_UserAuth\Actions\Login;

use App\Modules\User\_UserAuth\Models\UserAuth;

final readonly class LoginOutput
{
    public function __construct(
        public UserAuth $user,
        public string $token,
    ) {}
}
