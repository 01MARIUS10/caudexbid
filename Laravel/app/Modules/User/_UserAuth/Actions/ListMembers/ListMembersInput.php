<?php

namespace App\Modules\User\_UserAuth\Actions\ListMembers;

use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Filtres : ids, identifiants, grades. Aucun filtre = tous les membres.
 */
final readonly class ListMembersInput
{
    /**
     * @param  list<int>|null  $ids
     * @param  list<string>|null  $identifiants
     * @param  list<GradeCode>|null  $grades
     */
    public function __construct(
        public ?array $ids = null,
        public ?array $identifiants = null,
        public ?array $grades = null,
    ) {}

    public static function rules(): array
    {
        return [
            'ids' => ['nullable', 'array'],
            'ids.*' => ['integer'],
            'identifiants' => ['nullable', 'array'],
            'identifiants.*' => ['string'],
            'grades' => ['nullable', 'array'],
            'grades.*' => [Rule::enum(GradeCode::class)],
        ];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data = []): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self(
            ids: isset($v['ids']) ? array_map('intval', $v['ids']) : null,
            identifiants: $v['identifiants'] ?? null,
            grades: isset($v['grades']) ? array_map(fn ($g) => $g instanceof GradeCode ? $g : GradeCode::from($g), $v['grades']) : null,
        );
    }
}
