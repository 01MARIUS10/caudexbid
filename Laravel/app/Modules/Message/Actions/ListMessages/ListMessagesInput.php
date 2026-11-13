<?php

namespace App\Modules\Message\Actions\ListMessages;

use App\Modules\Message\Models\Enums\MessageType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final readonly class ListMessagesInput
{
    public function __construct(
        public ?int $membreId = null,
        public ?MessageType $type = null,
        public int $limit = 10,
    ) {}

    public static function rules(): array
    {
        return [
            'membre_id' => ['nullable', 'integer'],
            'type' => ['nullable', Rule::enum(MessageType::class)],
            'limit' => ['nullable', 'integer', 'between:1,200'],
        ];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self(
            membreId: isset($v['membre_id']) ? (int) $v['membre_id'] : null,
            type: isset($v['type']) ? MessageType::from($v['type']) : null,
            limit: (int) ($v['limit'] ?? 10),
        );
    }
}
