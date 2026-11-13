<?php

namespace App\Modules\Message\Actions\SendSystemMessage;

use Illuminate\Support\Facades\Validator;

/**
 * Message système (adjudication, etc.), envoyé au nom du compte administrateur.
 */
final readonly class SendSystemMessageInput
{
    public function __construct(
        public int $destinataireId,
        public string $memo,
    ) {}

    public static function rules(): array
    {
        return [
            'destinataire_id' => ['required', 'integer', 'exists:user_auths,id'],
            'memo' => ['required', 'string', 'max:5000'],
        ];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self((int) $v['destinataire_id'], $v['memo']);
    }
}
