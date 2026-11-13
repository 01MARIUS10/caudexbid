<?php

namespace App\Modules\Product\Actions\ListUnsettledAwards;

use Illuminate\Support\Facades\Validator;

/**
 * Filtre : adjudicataires (ids de membres). Aucun filtre = toutes les adjudications non soldées.
 */
final readonly class ListUnsettledAwardsInput
{
    /** @param list<int>|null $adjudicataireIds */
    public function __construct(public ?array $adjudicataireIds = null) {}

    public static function rules(): array
    {
        return [
            'adjudicataire_ids' => ['nullable', 'array'],
            'adjudicataire_ids.*' => ['integer'],
        ];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data = []): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self(isset($v['adjudicataire_ids']) ? array_map('intval', $v['adjudicataire_ids']) : null);
    }
}
