<?php

namespace App\Modules\User\_UserAuth\Actions\Logout;

use App\Modules\User\_UserAuth\Models\UserAuth;
use Illuminate\Support\Facades\Validator;

final readonly class LogoutInput
{
    public function __construct(
        public UserAuth $user,
        public bool $allDevices,
    ) {}

    public static function rules(): array
    {
        return ['all_devices' => ['nullable', 'boolean']];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data, UserAuth $user): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self($user, (bool) ($v['all_devices'] ?? false));
    }
}
