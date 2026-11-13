<?php

namespace App\Modules\Product\Actions\CreateProductOwner;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Validator;

final class CreateProductOwnerValidation
{
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:150'],
            'pays' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'infos_bancaires' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function validate(array $data, UserAuth $actor): CreateProductOwnerInput
    {
        if (! $actor->isAdmin()) {
            throw BusinessRuleException::forbidden();
        }

        return CreateProductOwnerInput::fromArray(Validator::make($data, $this->rules())->validate());
    }
}
