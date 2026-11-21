<?php

namespace App\Modules\Product\Actions\RelistProducts;

final readonly class RelistProductsInput
{
    public function __construct(
        /** Session source ; null = la session précédente qui contient des lots. */
        public ?int $fromSessionId = null,
        /** Session cible ; null = la session actuelle (ouverte, sinon la prochaine programmée). */
        public ?int $toSessionId = null,
        /** false : seulement les lots invendus · true : tous les lots (adjugés et vendus compris). */
        public bool $all = false,
        /** true : nouveaux lots en brouillon au lieu de « En vente ». */
        public bool $draft = false,
        /** Simulation : n'écrit rien. */
        public bool $dryRun = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            fromSessionId: isset($data['from']) ? (int) $data['from'] : null,
            toSessionId: isset($data['to']) ? (int) $data['to'] : null,
            all: (bool) ($data['all'] ?? false),
            draft: (bool) ($data['draft'] ?? false),
            dryRun: (bool) ($data['dry_run'] ?? false),
        );
    }
}
