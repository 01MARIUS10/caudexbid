<?php

namespace App\Modules\Product\ViewModels;

use App\Modules\Product\Models\Product;
use App\Modules\Product\ViewModels\Concerns\ProductVisibility;
use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Support\MemberDirectory;
use App\Shared\ViewModels\ViewModel;

/**
 * Carte résumée d'un lot : photo 1, nom, taille (description 1), prix actuel, temps restant, nb d'enchérisseurs.
 *
 * @mixin \App\Modules\Product\Models\Product
 */
class ProductCardViewModel extends ViewModel
{
    use ProductVisibility;

    /** @param Product $resource lot avec media et achat chargés */
    public function __construct(Product $resource, protected readonly ?UserAuth $viewer, protected readonly MemberDirectory $members)
    {
        parent::__construct($resource);
    }

    protected function present(): array
    {
        if ($this->isSoldState($this->resource)) {
            return $this->soldPayload($this->resource);
        }

        $photo1 = $this->media->firstWhere('position', 1);

        return [
            'id' => $this->id,
            'numero_lot' => $this->numero_lot,
            'nom' => $this->nom,
            'taille' => $this->description_1,
            'photo_1' => $photo1?->url(),
            'statut' => $this->statut->value,
            ...$this->pricePayload($this->resource),
            ...$this->timingPayload($this->resource),
        ];
    }
}
