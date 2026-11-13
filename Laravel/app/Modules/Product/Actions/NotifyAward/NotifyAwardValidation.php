<?php

namespace App\Modules\Product\Actions\NotifyAward;

use App\Modules\Product\Models\Product;
use App\Shared\Exceptions\BusinessRuleException;

final class NotifyAwardValidation
{
    public function validate(NotifyAwardInput $input): void
    {
        if (! Product::whereKey($input->produitId)->exists()) {
            throw new BusinessRuleException('Produit introuvable.', 404);
        }
    }
}
