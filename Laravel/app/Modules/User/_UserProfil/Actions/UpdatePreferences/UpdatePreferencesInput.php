<?php

namespace App\Modules\User\_UserProfil\Actions\UpdatePreferences;

use Illuminate\Support\Facades\Validator;

final readonly class UpdatePreferencesInput
{
    /** @param array<string, mixed> $attributes */
    public function __construct(
        public int $membreId,
        public array $attributes,
    ) {}

    public static function rules(): array
    {
        return [
            'membre_id' => ['required', 'integer', 'exists:user_auths,id'],
            'fuseau_horaire' => ['sometimes', 'required', 'timezone:all'],
            'notif_surenchere' => ['sometimes', 'boolean'],
            'notif_adjudication' => ['sometimes', 'boolean'],
            'notif_nouveau_message' => ['sometimes', 'boolean'],
            'notif_prochaine_session' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data, int $membreId): self
    {
        $v = Validator::make(['membre_id' => $membreId] + $data, self::rules(), self::messages())->validate();
        unset($v['membre_id']);

        return new self($membreId, $v);
    }
}
