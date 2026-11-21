<?php

namespace App\Modules\Product\Models;

use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\_ProductAchat\Models\ProductAchat;
use App\Modules\Product\_ProductBid\Models\ProductBid;
use App\Modules\Product\_ProductBid\Models\ProductProxyPlafondBid;
use App\Modules\Product\_ProductMedia\Models\ProductMedia;
use App\Modules\Product\_ProductPlant\Models\ProductPlant;
use App\Shared\Support\Money;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Table « Produit » (un lot) — supprimée après règlement.
 *
 * Membres (module User) : clés étrangères seulement. Le controller attache « auteurPrixActuel » via MemberAttacher.
 *
 * @property int $id
 * @property string $numero_lot
 * @property string $nom
 * @property string|null $prix_actuel
 * @property string $prix_depart
 * @property string $pas_enchere
 * @property int|null $auteur_prix_actuel_id
 * @property int $nb_prolongations
 * @property ProductStatus $statut
 * @property \Illuminate\Support\Carbon $debut_enchere_le
 * @property \Illuminate\Support\Carbon $cloture_prevue_le
 * @property \Illuminate\Support\Carbon $cloture_effective_le
 */
class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'session_enchere_id',
        'proprietaire_id',
        'plante_id',
        'categorie',
        'numero_lot',
        'nom',
        'description_1',
        'description_2',
        'description_3',
        'taille_diametre',
        'taille_circonference',
        'taille_hauteur',
        'prix_vente_existant',
        'prix_depart',
        'pas_enchere',
        'debut_enchere_le',
        'cloture_prevue_le',
        'cloture_effective_le',
        'nb_prolongations',
        'prix_actuel',
        'auteur_prix_actuel_id',
        'nb_encherisseurs',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'taille_diametre' => 'decimal:2',
            'taille_circonference' => 'decimal:2',
            'taille_hauteur' => 'decimal:2',
            'prix_vente_existant' => 'decimal:2',
            'prix_depart' => 'decimal:2',
            'pas_enchere' => 'decimal:2',
            'prix_actuel' => 'decimal:2',
            'debut_enchere_le' => 'datetime',
            'cloture_prevue_le' => 'datetime',
            'cloture_effective_le' => 'datetime',
            'nb_prolongations' => 'integer',
            'nb_encherisseurs' => 'integer',
            'statut' => ProductStatus::class,
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ProductAuctionSession::class, 'session_enchere_id');
    }

    /** Plante du catalogue mise en vente dans ce lot (ses informations sont recopiées dans le lot). */
    public function plante(): BelongsTo
    {
        return $this->belongsTo(ProductPlant::class, 'plante_id');
    }

    public function proprietaire(): BelongsTo
    {
        return $this->belongsTo(ProductOwner::class, 'proprietaire_id');
    }

    /** Photos 1-4 puis vidéo (ordre d'affichage P1, P2, P3, P4, V1). */
    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class, 'produit_id')
            ->orderByRaw("CASE WHEN type = 'photo' THEN 0 ELSE 1 END")
            ->orderBy('position');
    }

    public function achat(): HasOne
    {
        return $this->hasOne(ProductAchat::class, 'produit_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(ProductBid::class, 'produit_id');
    }

    public function proxyPlafonds(): HasMany
    {
        return $this->hasMany(ProductProxyPlafondBid::class, 'produit_id');
    }

    /** Le prix et le nombre d'enchérisseurs ne sont publiés que dans les 48 h avant la clôture. */
    public function isPriceVisible(?CarbonInterface $now = null): bool
    {
        $now ??= now();
        $hours = (int) config('caudexbid.price_visibility_hours');

        return $now->greaterThanOrEqualTo($this->cloture_effective_le->copy()->subHours($hours));
    }

    public function isOpenForBids(?CarbonInterface $now = null): bool
    {
        $now ??= now();

        return $this->statut === ProductStatus::Active
            && $now->greaterThanOrEqualTo($this->debut_enchere_le)
            && $now->lessThan($this->cloture_effective_le);
    }

    /** Prochaine enchère minimale : prix actuel + pas, ou prix de départ s'il n'y a aucune enchère. */
    public function prochaineEnchereMinimaleCents(): int
    {
        return $this->prix_actuel === null
            ? Money::toCents($this->prix_depart)
            : Money::toCents($this->prix_actuel) + Money::toCents($this->pas_enchere);
    }

    /** Membre adjudicataire : acheteur immédiat ou auteur du prix actuel à la clôture. */
    public function adjudicataireId(): ?int
    {
        return match ($this->statut) {
            ProductStatus::Sold => $this->achat?->acheteur_id,
            ProductStatus::Awarded => $this->auteur_prix_actuel_id,
            default => null,
        };
    }

    /** Prix d'adjudication : prix d'achat immédiat ou prix actuel à la clôture. */
    public function prixAdjudication(): ?string
    {
        return match ($this->statut) {
            ProductStatus::Sold => $this->achat?->prix,
            ProductStatus::Awarded => $this->prix_actuel,
            default => null,
        };
    }
}
