<?php

namespace App\Modules\Product\_ProductPlant\Actions\SetPlantImage;

use App\Modules\Product\_ProductPlant\Models\ProductPlant;
use App\Modules\Product\_ProductPlant\Repository\ProductPlantRepository;

/**
 * Ajoute ou remplace l'image d'une plante de la collection (l'ancienne image est supprimée).
 */
final class SetPlantImageAction
{
    public function __construct(private readonly ProductPlantRepository $plants) {}

    public function execute(SetPlantImageInput $input): ProductPlant
    {
        return $this->plants->storeImage($this->plants->find($input->planteId), $input->image);
    }
}
