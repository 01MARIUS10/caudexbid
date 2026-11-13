<?php

namespace App\Modules\User\_UserProfil\Actions\UpdateProfil;

use App\Modules\User\_UserProfil\Models\Enums\Nationality;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * « Modification des informations personnelles » : seuls les champs envoyés sont modifiés.
 * Le parrain n'est pas modifiable ici (il change uniquement via « Changement de grade »).
 */
final readonly class UpdateProfilInput
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
            'nom' => ['sometimes', 'nullable', 'string', 'max:100'],
            'telephone' => ['sometimes', 'required', 'string', 'max:30'],
            'adresse' => ['sometimes', 'required', 'string', 'max:1000'],
            'pays' => ['sometimes', 'nullable', 'string', 'max:100'],
            'nationalite' => ['sometimes', 'required', Rule::enum(Nationality::class)],
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

        if (isset($v['nationalite'])) {
            $v['nationalite'] = Nationality::from($v['nationalite']);
        }

        return new self($membreId, $v);
    }
}
