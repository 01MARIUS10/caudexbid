<?php

namespace App\Modules\Message\Actions\ListMemberRequests;

use Illuminate\Support\Facades\Validator;

final readonly class ListMemberRequestsInput
{
    public function __construct(
        public int $premiumId,
        public ?int $requesterId = null,
    ) {}

    public static function rules(): array
    {
        return [
            'premium_id' => ['required', 'integer'],
            'requester_id' => ['nullable', 'integer'],
        ];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self((int) $v['premium_id'], isset($v['requester_id']) ? (int) $v['requester_id'] : null);
    }
}
