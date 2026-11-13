<?php

namespace App\Modules\User\_UserAuth\Actions\FindMember;

use Illuminate\Support\Facades\Validator;

final readonly class FindMemberInput
{
    public function __construct(public int $membreId) {}

    public static function rules(): array
    {
        return ['membre_id' => ['required', 'integer', 'exists:user_auths,id']];
    }

    public static function messages(): array
    {
        return ['membre_id.exists' => 'Membre introuvable.'];
    }

    public static function fromArray(array $data): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self((int) $v['membre_id']);
    }
}
