<?php

namespace App\Modules\Product\Actions\RelistProducts;

use Illuminate\Support\Facades\Validator;

final class RelistProductsValidation
{
    public function rules(): array
    {
        return [
            'from' => ['nullable', 'integer', 'exists:product_auction_sessions,id'],
            'to' => ['nullable', 'integer', 'exists:product_auction_sessions,id', 'different:from'],
            'all' => ['nullable', 'boolean'],
            'draft' => ['nullable', 'boolean'],
            'dry_run' => ['nullable', 'boolean'],
        ];
    }

    public function validate(array $data = []): RelistProductsInput
    {
        return RelistProductsInput::fromArray(Validator::make(array_filter($data, fn ($v) => $v !== null), $this->rules())->validate());
    }
}
