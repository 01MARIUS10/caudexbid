<?php

namespace App\Modules\Product\_ProductPlant\Actions\FillPlantImages;

use Illuminate\Support\Facades\Validator;

final class FillPlantImagesValidation
{
    public function rules(): array
    {
        return [
            'all' => ['nullable', 'boolean'],
            'dry_run' => ['nullable', 'boolean'],
        ];
    }

    public function validate(array $data = []): FillPlantImagesInput
    {
        return FillPlantImagesInput::fromArray(Validator::make($data, $this->rules())->validate());
    }
}
