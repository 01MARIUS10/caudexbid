<?php

namespace App\Modules\Product\Actions\CreateProduct;

final readonly class CreateProductInput
{
    /** @param array<string, mixed> $attributes colonnes de products */
    public function __construct(
        public int $sessionId,
        public array $attributes,
        public ?string $prixAchatImmediat,
        public bool $publier,
    ) {}

    public static function fromArray(array $data): self
    {
        $attributes = collect($data)->only([
            'proprietaire_id', 'plante_id', 'categorie', 'numero_lot', 'nom',
            'description_1', 'description_2', 'description_3',
            'taille_diametre', 'taille_circonference', 'taille_hauteur',
            'prix_vente_existant', 'prix_depart', 'pas_enchere',
        ])->all();

        return new self(
            sessionId: (int) $data['session_enchere_id'],
            attributes: $attributes,
            prixAchatImmediat: isset($data['prix_achat_immediat']) ? (string) $data['prix_achat_immediat'] : null,
            publier: (bool) ($data['publier'] ?? true),
        );
    }
}
