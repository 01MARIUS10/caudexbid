<?php

namespace App\Modules\Product\_ProductBid\Actions\PlaceBid;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Validator;

/**
 * - Seuls les grades M et P peuvent enchérir (le membre général ne peut pas cliquer sur « Enchérir »).
 * - Si « Proxy bid » est coché, le plafond est obligatoire et >= montant.
 */
final class PlaceBidValidation
{
    public function rules(): array
    {
        return [
            'produit_id' => ['required', 'integer', 'exists:products,id'],
            'montant' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'proxy' => ['nullable', 'boolean'],
            'plafond' => ['required_if_accepted:proxy', 'nullable', 'numeric', 'gte:montant', 'decimal:0,2'],
        ];
    }

    public function messages(): array
    {
        return [
            'plafond.required_if_accepted' => 'Si « Proxy bid » est coché, vous devez indiquer un plafond.',
            'plafond.gte' => 'Le plafond doit être supérieur ou égal au montant de l\'enchère.',
        ];
    }

    public function validate(array $data, UserAuth $membre): PlaceBidInput
    {
        if (! $membre->canBid()) {
            throw BusinessRuleException::forbidden('Seuls les membres réguliers et premium peuvent enchérir.');
        }

        return PlaceBidInput::fromArray(Validator::make($data, $this->rules(), $this->messages())->validate(), $membre);
    }
}
