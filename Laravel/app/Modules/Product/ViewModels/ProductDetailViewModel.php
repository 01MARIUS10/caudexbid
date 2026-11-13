<?php

namespace App\Modules\Product\ViewModels;

use App\Modules\Product\ViewModels\Concerns\ProductVisibility;
use App\Modules\Product\_ProductBid\Models\ProductProxyPlafondBid;
use App\Modules\Product\Models\Product;
use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Support\MemberDirectory;
use App\Modules\Product\_ProductMedia\ViewModels\ProductMediaViewModel;
use App\Shared\ViewModels\ViewModel;

/**
 * Écran de détail (clic sur la photo) et écran d'enchère.
 *
 * @mixin \App\Modules\Product\Models\Product
 */
class ProductDetailViewModel extends ViewModel
{
    use ProductVisibility;

    /**
     * @param Product $resource lot avec media et achat chargés
     * @param ProductProxyPlafondBid|null $plafond plafond proxy du spectateur sur ce lot (lu par le controller via le Repository)
     */
    public function __construct(Product $resource, protected readonly ?UserAuth $viewer, protected readonly MemberDirectory $members, private readonly ?ProductProxyPlafondBid $plafond = null)
    {
        parent::__construct($resource);
    }

    protected function present(): array
    {
        if ($this->isSoldState($this->resource)) {
            return $this->soldPayload($this->resource);
        }

        $viewer = $this->viewer;
        $plafond = $this->plafond;

        return [
            'id' => $this->id,
            'numero_lot' => $this->numero_lot,
            'nom' => $this->nom,
            'categorie' => $this->categorie,
            'description_1' => $this->description_1,
            'description_2' => $this->description_2,
            'description_3' => $this->description_3,
            'taille_diametre' => $this->taille_diametre,
            'taille_circonference' => $this->taille_circonference,
            'taille_hauteur' => $this->taille_hauteur,
            'pas_enchere' => $this->pas_enchere,
            'statut' => $this->statut->value,
            ...$this->pricePayload($this->resource),
            ...$this->timingPayload($this->resource),
            // Le bouton « Enchérir » n'est pas cliquable pour un membre général.
            'peut_encherir' => (bool) $viewer?->canBid() && $this->isOpenForBids(),
            'est_meilleur_encherisseur' => $viewer && $this->auteur_prix_actuel_id === $viewer->id,
            'mon_plafond' => $this->when($plafond !== null, fn () => [
                'plafond' => $plafond->plafond,
                'est_actif' => $plafond->est_actif,
            ]),
            // Ordre : photo 1, P2, P3, P4, V1.
            'medias' => ProductMediaViewModel::collection($this->media),
        ];
    }
}
