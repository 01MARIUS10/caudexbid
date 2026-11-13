<?php

namespace App\Modules\User\_UserAuth\Actions\Register;

use App\Modules\User\_UserProfil\Models\Enums\Nationality;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Inscription : identifiant* / mot de passe* / coordonnées* / identifiant du parrain* (+ nationalité).
 */
final readonly class RegisterInput
{
    public function __construct(
        public string $identifiant,
        public string $email,
        public string $password,
        public ?string $nom,
        public string $telephone,
        public string $adresse,
        public ?string $pays,
        public Nationality $nationalite,
        public string $parrainIdentifiant,
    ) {}

    public static function rules(): array
    {
        return [
            'identifiant' => ['required', 'string', 'alpha_dash', 'min:3', 'max:50', 'unique:user_auths,identifiant'],
            'email' => ['required', 'email', 'max:150', 'unique:user_auths,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'nom' => ['nullable', 'string', 'max:100'],
            'telephone' => ['required', 'string', 'max:30'],
            'adresse' => ['required', 'string', 'max:1000'],
            'pays' => ['nullable', 'string', 'max:100'],
            'nationalite' => ['required', Rule::enum(Nationality::class)],
            'parrain_identifiant' => ['required', 'string', 'exists:user_auths,identifiant'],
        ];
    }

    public static function messages(): array
    {
        return [
            'parrain_identifiant.exists' => 'Ce parrain n\'existe pas.',
        ];
    }

    public static function fromArray(array $data): self
    {
        $v = Validator::make($data, self::rules(), self::messages())->validate();

        return new self(
            identifiant: $v['identifiant'],
            email: $v['email'],
            password: $v['password'],
            nom: $v['nom'] ?? null,
            telephone: $v['telephone'],
            adresse: $v['adresse'],
            pays: $v['pays'] ?? null,
            nationalite: Nationality::from($v['nationalite']),
            parrainIdentifiant: $v['parrain_identifiant'],
        );
    }
}
