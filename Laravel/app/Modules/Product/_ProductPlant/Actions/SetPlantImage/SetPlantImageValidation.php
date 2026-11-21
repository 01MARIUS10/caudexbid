<?php

namespace App\Modules\Product\_ProductPlant\Actions\SetPlantImage;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Validator;

final class SetPlantImageValidation
{
    public function rules(): array
    {
        return [
            'plante_id' => ['required', 'integer', 'exists:product_plants,id'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    public function validate(array $data, UserAuth $actor): SetPlantImageInput
    {
        if (! $actor->isAdmin()) {
            throw BusinessRuleException::forbidden();
        }

        return SetPlantImageInput::fromArray(Validator::make($data, $this->rules())->validate());
    }
}
