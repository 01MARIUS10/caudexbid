<?php

namespace App\Modules\User\_UserGrad\Actions\ChangeGrade;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

final readonly class ChangeGradeInput
{
    public function __construct(
        public UserAuth $actor,
        public int $targetUserId,
        public GradeCode $newGrade,
    ) {}

    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:user_auths,id'],
            'grade_code' => ['required', Rule::enum(GradeCode::class)],
        ];
    }

    public static function messages(): array
    {
        return ['user_id.exists' => 'Membre introuvable.'];
    }

    public static function fromArray(array $data, UserAuth $actor): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self($actor, (int) $v['user_id'], GradeCode::from($v['grade_code']));
    }
}
