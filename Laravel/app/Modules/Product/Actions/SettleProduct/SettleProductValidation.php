<?php

namespace App\Modules\Product\Actions\SettleProduct;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Validator;

final class SettleProductValidation
{
    public function rules(): array
    {
        return [
            'produit_id' => ['required', 'integer', 'exists:products,id'],
        ];
    }

    public function validate(array $data, UserAuth $actor): SettleProductInput
    {
        if (! $actor->isAdmin()) {
            throw BusinessRuleException::forbidden();
        }

        return SettleProductInput::fromArray(Validator::make($data, $this->rules())->validate());
    }
}
