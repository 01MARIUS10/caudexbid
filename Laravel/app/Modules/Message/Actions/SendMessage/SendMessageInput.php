<?php

namespace App\Modules\Message\Actions\SendMessage;

use App\Modules\Message\Models\Enums\MessageType;
use App\Modules\User\_UserAuth\Models\UserAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final readonly class SendMessageInput
{
    public function __construct(
        public UserAuth $expediteur,
        public int $destinataireId,
        public MessageType $type,
        public string $memo,
    ) {}

    public static function rules(int $expediteurId): array
    {
        return [
            'destinataire_id' => ['required', 'integer', 'exists:user_auths,id', Rule::notIn([$expediteurId])],
            'type' => ['nullable', Rule::enum(MessageType::class)],
            'memo' => ['required', 'string', 'max:5000'],
        ];
    }

    public static function messages(): array
    {
        return ['destinataire_id.not_in' => 'Vous ne pouvez pas vous envoyer un message.'];
    }

    public static function fromArray(array $data, UserAuth $expediteur): self
    {
        $v = Validator::make($data, self::rules($expediteur->id), self::messages())->validate();

        return new self(
            expediteur: $expediteur,
            destinataireId: (int) $v['destinataire_id'],
            type: MessageType::from($v['type'] ?? MessageType::General->value),
            memo: $v['memo'],
        );
    }
}
