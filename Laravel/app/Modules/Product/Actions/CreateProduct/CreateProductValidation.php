<?php

namespace App\Modules\Product\Actions\CreateProduct;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Validator;

final class CreateProductValidation
{
    public function rules(): array
    {
        return [
            'session_enchere_id' => ['required', 'integer', 'exists:product_auction_sessions,id'],
            'proprietaire_id' => ['required', 'integer', 'exists:product_owners,id'],
            // Plante de la collection (facultative) : classement et statistiques.
            'plante_id' => ['nullable', 'integer', 'exists:product_plants,id'],
            'categorie' => ['nullable', 'string', 'max:50'],
            'numero_lot' => ['required', 'string', 'max:20', 'unique:products,numero_lot'],
            'nom' => ['required', 'string', 'max:150'],
            'description_1' => ['nullable', 'string'],
            'description_2' => ['nullable', 'string'],
            'description_3' => ['nullable', 'string'],
            'taille_diametre' => ['nullable', 'numeric', 'min:0'],
            'taille_circonference' => ['nullable', 'numeric', 'min:0'],
            'taille_hauteur' => ['nullable', 'numeric', 'min:0'],
            'prix_vente_existant' => ['nullable', 'numeric', 'min:0'],
            'prix_depart' => ['required', 'numeric', 'min:0'],
            'pas_enchere' => ['required', 'numeric', 'gt:0'],
            'prix_achat_immediat' => ['nullable', 'numeric', 'gt:prix_depart'],
            'publier' => ['nullable', 'boolean'],
        ];
    }

    public function validate(array $data, UserAuth $actor): CreateProductInput
    {
        if (! $actor->isAdmin()) {
            throw BusinessRuleException::forbidden();
        }

        return CreateProductInput::fromArray(Validator::make($data, $this->rules())->validate());
    }
}
