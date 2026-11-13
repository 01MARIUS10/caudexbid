<?php

namespace App\Modules\Product\_ProductMedia\Actions\DeleteProductMedia;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Validator;

final class DeleteProductMediaValidation
{
    public function rules(): array
    {
        return [
            'media_id' => ['required', 'integer', 'exists:product_media,id'],
        ];
    }

    public function validate(array $data, UserAuth $actor): DeleteProductMediaInput
    {
        if (! $actor->isAdmin()) {
            throw BusinessRuleException::forbidden();
        }

        return DeleteProductMediaInput::fromArray(Validator::make($data, $this->rules())->validate());
    }
}
