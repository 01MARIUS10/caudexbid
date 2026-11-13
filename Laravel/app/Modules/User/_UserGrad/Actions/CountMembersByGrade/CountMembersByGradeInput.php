<?php

namespace App\Modules\User\_UserGrad\Actions\CountMembersByGrade;

final readonly class CountMembersByGradeInput
{
    public static function rules(): array
    {
        return [];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data = []): self
    {
        return new self();
    }
}
