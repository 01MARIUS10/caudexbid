<?php

namespace App\Modules\User\_UserProfil\Actions\ListSponsoredMembers;

use Illuminate\Support\Facades\Validator;

final readonly class ListSponsoredMembersInput
{
    public function __construct(public int $parrainId) {}

    public static function rules(): array
    {
        return ['parrain_id' => ['required', 'integer', 'exists:user_auths,id']];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self((int) $v['parrain_id']);
    }
}
