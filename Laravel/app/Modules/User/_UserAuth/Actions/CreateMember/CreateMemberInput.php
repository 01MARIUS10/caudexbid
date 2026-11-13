<?php

namespace App\Modules\User\_UserAuth\Actions\CreateMember;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use App\Modules\User\_UserProfil\Models\Enums\Nationality;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Création d'un membre par l'administrateur (grade et parrain choisis directement).
 */
final readonly class CreateMemberInput
{
    public function __construct(
        public UserAuth $admin,
        public string $identifiant,
        public string $email,
        public string $password,
        public ?string $nom,
        public ?string $telephone,
        public ?string $adresse,
        public ?string $pays,
        public Nationality $nationalite,
        public GradeCode $grade,
        public ?int $parrainId,
    ) {}

    public static function rules(): array
    {
        return [
            'identifiant' => ['required', 'string', 'alpha_dash', 'min:3', 'max:50', 'unique:user_auths,identifiant'],
            'email' => ['required', 'email', 'max:150', 'unique:user_auths,email'],
            'password' => ['required', Password::min(8)],
            'nom' => ['nullable', 'string', 'max:100'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'adresse' => ['nullable', 'string', 'max:1000'],
            'pays' => ['nullable', 'string', 'max:100'],
            'nationalite' => ['required', Rule::enum(Nationality::class)],
            'grade_code' => ['required', Rule::enum(GradeCode::class)],
            'parrain_id' => ['nullable', 'integer', 'exists:user_auths,id'],
        ];
    }

    public static function messages(): array
    {
        return [];
    }

    public static function fromArray(array $data, UserAuth $admin): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self(
            admin: $admin,
            identifiant: $v['identifiant'],
            email: $v['email'],
            password: $v['password'],
            nom: $v['nom'] ?? null,
            telephone: $v['telephone'] ?? null,
            adresse: $v['adresse'] ?? null,
            pays: $v['pays'] ?? null,
            nationalite: Nationality::from($v['nationalite']),
            grade: GradeCode::from($v['grade_code']),
            parrainId: isset($v['parrain_id']) ? (int) $v['parrain_id'] : null,
        );
    }
}
