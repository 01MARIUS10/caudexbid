<?php

namespace App\Modules\User\_UserAuth\Actions\WebLogin;

use Illuminate\Support\Facades\Validator;

final readonly class WebLoginInput
{
    public function __construct(
        public string $identifiant,
        public string $password,
        public bool $remember,
    ) {}

    public static function rules(): array
    {
        return [
            'identifiant' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self($v['identifiant'], $v['password'], (bool) ($v['remember'] ?? false));
    }
}
