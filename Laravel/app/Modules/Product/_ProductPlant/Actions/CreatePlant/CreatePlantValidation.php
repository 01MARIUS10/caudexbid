<?php

namespace App\Modules\Product\_ProductPlant\Actions\CreatePlant;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Validator;

final class CreatePlantValidation
{
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:150', 'unique:product_plants,nom'],
            'genre' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return ['nom.unique' => 'Cette plante existe déjà dans la collection.'];
    }

    public function validate(array $data, UserAuth $actor): CreatePlantInput
    {
        if (! $actor->isAdmin()) {
            throw BusinessRuleException::forbidden();
        }

        return CreatePlantInput::fromArray(Validator::make($data, $this->rules(), $this->messages())->validate());
    }
}
