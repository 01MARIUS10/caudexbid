<?php

namespace App\Modules\Message\Actions\CountUnreadMessages;

use Illuminate\Support\Facades\Validator;

final readonly class CountUnreadMessagesInput
{
    public function __construct(public int $destinataireId) {}

    public static function rules(): array
    {
        return ['destinataire_id' => ['required', 'integer']];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self((int) $v['destinataire_id']);
    }
}
