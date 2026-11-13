<?php

namespace App\Modules\Product\_ProductAchat\Actions\BuyNow;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Validator;

/**
 * Case « Décider l'achat immédiat » : une confirmation est demandée avant l'achat.
 */
final class BuyNowValidation
{
    public function rules(): array
    {
        return [
            'produit_id' => ['required', 'integer', 'exists:products,id'],
            'confirmation' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'confirmation.accepted' => 'Vous devez confirmer l\'achat immédiat.',
        ];
    }

    public function validate(array $data, UserAuth $acheteur): BuyNowInput
    {
        if (! $acheteur->canBid()) {
            throw BusinessRuleException::forbidden('Seuls les membres réguliers et premium peuvent acheter.');
        }

        return BuyNowInput::fromArray(Validator::make($data, $this->rules(), $this->messages())->validate(), $acheteur);
    }
}
