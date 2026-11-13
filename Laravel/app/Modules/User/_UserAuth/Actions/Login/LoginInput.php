<?php

namespace App\Modules\User\_UserAuth\Actions\Login;

use Illuminate\Support\Facades\Validator;

final readonly class LoginInput
{
    public function __construct(
        public string $identifiant,
        public string $password,
        public string $deviceName,
    ) {}

    public static function rules(): array
    {
        return [
            'identifiant' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self($v['identifiant'], $v['password'], $v['device_name'] ?? 'web');
    }
}
