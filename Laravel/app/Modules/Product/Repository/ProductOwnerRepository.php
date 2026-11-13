<?php

namespace App\Modules\Product\Repository;

use App\Modules\Product\Models\ProductOwner;
use Illuminate\Support\Collection;

class ProductOwnerRepository
{
    public function all(): Collection
    {
        return ProductOwner::orderBy('nom')->get();
    }

    public function create(array $attributes): ProductOwner
    {
        return ProductOwner::create($attributes);
    }
}
