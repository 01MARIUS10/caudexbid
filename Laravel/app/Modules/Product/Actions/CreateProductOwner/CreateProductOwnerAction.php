<?php

namespace App\Modules\Product\Actions\CreateProductOwner;

use App\Modules\Product\Models\ProductOwner;
use App\Modules\Product\Repository\ProductOwnerRepository;

final class CreateProductOwnerAction
{
    public function __construct(private readonly ProductOwnerRepository $owners) {}

    public function execute(CreateProductOwnerInput $input): ProductOwner
    {
        return $this->owners->create([
            'nom' => $input->nom,
            'pays' => $input->pays,
            'email' => $input->email,
            'infos_bancaires' => $input->infosBancaires,
        ]);
    }
}
