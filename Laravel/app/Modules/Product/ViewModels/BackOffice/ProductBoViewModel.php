<?php

namespace App\Modules\Product\ViewModels\BackOffice;

use App\Modules\Product\_ProductMedia\ViewModels\ProductMediaViewModel;
use App\Modules\Product\Models\Product;
use App\Shared\Support\MemberDirectory;
use App\Shared\Support\Money;
use App\Shared\ViewModels\ViewModel;

/**
 * Lot tel qu'affiché dans le back office (liste, fiche, tableau de bord). Lu par les vues via toArray().
 *
 * @mixin \App\Modules\Product\Models\Product
 */
class ProductBoViewModel extends ViewModel
{
    /** @param Product $resource lot avec media, session, achat et proprietaire chargés */
    public function __construct(Product $resource, protected readonly MemberDirectory $members)
    {
        parent::__construct($resource);
    }

    protected function present(): array
    {
        $achat = $this->achat;
        $medias = $this->relationLoaded('media') ? $this->media : collect();

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
            'statut' => $this->statut->value,
            'est_vente_non_reglee' => $this->statut->isUnsettledSale(),
            'session' => $this->session?->titre,
            'proprietaire' => $this->relationLoaded('proprietaire') ? $this->proprietaire?->nom : null,
            'plante_id' => $this->plante_id,
            'plante' => $this->relationLoaded('plante') ? $this->plante?->nom : null,
            'prix_depart' => $this->prix_depart,
            'pas_enchere' => $this->pas_enchere,
            'prix_vente_existant' => $this->prix_vente_existant,
            'prix_actuel' => $this->prix_actuel,
            'auteur_prix_actuel' => $this->members->identifiant($this->auteur_prix_actuel_id),
            'prochaine_enchere_minimale' => Money::fromCents($this->prochaineEnchereMinimaleCents()),
            'nb_encherisseurs' => $this->nb_encherisseurs,
            'debut_enchere_le' => $this->debut_enchere_le,
            'cloture_prevue_le' => $this->cloture_prevue_le,
            'cloture_effective_le' => $this->cloture_effective_le,
            'nb_prolongations' => $this->nb_prolongations,
            'prix_publics' => $this->isPriceVisible(),
            'prix_adjudication' => $this->prixAdjudication(),
            'achat' => $achat ? [
                'prix' => $achat->prix,
                'acheteur' => $this->members->identifiant($achat->acheteur_id),
                'achete_le' => $achat->achete_le,
                'est_achete' => $achat->isBought(),
            ] : null,
            'photo_1' => $medias->first(fn ($m) => $m->type->value === 'photo' && $m->position === 1)?->url(),
            'medias' => ProductMediaViewModel::collection($medias)->toArray(),
        ];
    }
}
