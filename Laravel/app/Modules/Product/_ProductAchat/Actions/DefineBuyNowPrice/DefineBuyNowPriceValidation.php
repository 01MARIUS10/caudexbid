<?php

namespace App\Modules\Product\_ProductAchat\Actions\DefineBuyNowPrice;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Validator;

final class DefineBuyNowPriceValidation
{
    public function rules(): array
    {
        return [
            'produit_id' => ['required', 'integer', 'exists:products,id'],
            'prix' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
        ];
    }

    public function validate(array $data, UserAuth $actor): DefineBuyNowPriceInput
    {
        if (! $actor->isAdmin()) {
            throw BusinessRuleException::forbidden();
        }

        return DefineBuyNowPriceInput::fromArray(Validator::make($data, $this->rules())->validate());
    }
}
