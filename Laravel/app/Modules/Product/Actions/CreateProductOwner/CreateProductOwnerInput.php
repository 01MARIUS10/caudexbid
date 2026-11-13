<?php

namespace App\Modules\Product\Actions\CreateProductOwner;

final readonly class CreateProductOwnerInput
{
    public function __construct(
        public string $nom,
        public ?string $pays,
        public ?string $email,
        public ?string $infosBancaires,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nom: $data['nom'],
            pays: $data['pays'] ?? 'Madagascar',
            email: $data['email'] ?? null,
            infosBancaires: $data['infos_bancaires'] ?? null,
        );
    }
}
