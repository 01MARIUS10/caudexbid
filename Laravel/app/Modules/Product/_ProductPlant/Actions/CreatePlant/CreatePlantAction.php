<?php

namespace App\Modules\Product\_ProductPlant\Actions\CreatePlant;

use App\Modules\Product\_ProductPlant\Models\ProductPlant;
use App\Modules\Product\_ProductPlant\Repository\ProductPlantRepository;

/**
 * Ajout d'une plante à la collection (information générale, réutilisée pour pré-remplir les produits),
 * avec son image facultative (identité visuelle de la collection).
 */
final class CreatePlantAction
{
    public function __construct(private readonly ProductPlantRepository $plants) {}

    public function execute(CreatePlantInput $input): ProductPlant
    {
        $plant = $this->plants->create($input->attributes);

        return $input->image ? $this->plants->storeImage($plant, $input->image) : $plant;
    }
}
