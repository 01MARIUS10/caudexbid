<?php

namespace App\Modules\Message\Actions\MarkMessageRead;

use Illuminate\Support\Facades\Validator;

final readonly class MarkMessageReadInput
{
    public function __construct(
        public int $readerId,
        public int $messageId,
    ) {}

    public static function rules(): array
    {
        return ['message_id' => ['required', 'integer', 'exists:messages,id']];
    }

    public static function messages(): array
    {
        return ['message_id.exists' => 'Message introuvable.'];
    }

    public static function fromArray(array $data, int $readerId): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self($readerId, (int) $v['message_id']);
    }
}
