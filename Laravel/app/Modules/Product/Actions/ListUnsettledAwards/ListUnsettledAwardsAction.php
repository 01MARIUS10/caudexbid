<?php

namespace App\Modules\Product\Actions\ListUnsettledAwards;

use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Support\Collection;

/**
 * Porte d'entrée du module Product : lots adjugés ou achetés pas encore réglés.
 */
final class ListUnsettledAwardsAction
{
    public function __construct(
        private readonly ListUnsettledAwardsValidation $validation,
        private readonly ProductRepository $products,
    ) {}

    /** @return Collection<int, \App\Modules\Product\Models\Product> */
    public function execute(ListUnsettledAwardsInput $input): Collection
    {
        $this->validation->validate($input);

        return $this->products->unsettled($input->adjudicataireIds);
    }
}
