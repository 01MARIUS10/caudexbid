<?php

namespace App\Modules\Product\Actions\CloseExpiredAuctions;

use Illuminate\Support\Facades\Validator;

final class CloseExpiredAuctionsValidation
{
    public function rules(): array
    {
        return [
            'now' => ['nullable', 'date'],
        ];
    }

    public function validate(array $data = []): CloseExpiredAuctionsInput
    {
        return CloseExpiredAuctionsInput::fromArray(Validator::make($data, $this->rules())->validate());
    }
}
