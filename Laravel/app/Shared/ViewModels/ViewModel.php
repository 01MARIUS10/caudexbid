<?php

namespace App\Shared\ViewModels;

use Closure;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\AbstractPaginator;
use JsonSerializable;

/**
 * ViewModel de présentation : transforme des données DÉJÀ chargées (modèles issus d'un Repository, valeurs calculées
 * par une Action) en tableau prêt à afficher. Il sert aussi bien à la réponse JSON de l'API qu'aux vues Blade.
 *
 * Règle : il formate seulement. Aucune requête, aucun appel à un Repository ni à une Action, aucune relation
 * chargée à la demande : tout ce dont il a besoin est fourni à la construction (relations chargées par le Repository,
 * valeurs complémentaires en paramètres).
 */
abstract class ViewModel implements JsonSerializable, Responsable
{
    private int $status = 200;

    public function __construct(protected readonly mixed $resource) {}

    /** Données présentées. Utiliser when() / whenLoaded() pour omettre une clé. */
    abstract protected function present(): array;

    public static function make(mixed ...$arguments): static
    {
        return new static(...$arguments);
    }

    /**
     * @param  iterable<mixed>  $items  modèles (ou paginator) déjà chargés
     * @param  Closure|null  $make  fabrique le ViewModel d'un élément quand le constructeur demande plus que l'élément
     */
    public static function collection(iterable $items, ?Closure $make = null): ViewModelCollection
    {
        $make ??= fn ($item) => new static($item);
        $paginator = $items instanceof AbstractPaginator ? $items : null;

        $list = [];
        foreach ($paginator ? $paginator->getCollection() : $items as $item) {
            $list[] = $make($item);
        }

        return new ViewModelCollection($list, $paginator);
    }

    /** Tableau formaté (les valeurs omises sont retirées, les ViewModel imbriqués sont convertis). */
    final public function toArray(): array
    {
        return self::clean($this->present());
    }

    /** Réponse HTTP 201 au lieu de 200. */
    public function created(): static
    {
        $clone = clone $this;
        $clone->status = 201;

        return $clone;
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json(['data' => $this->toArray()], $this->status);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /** La valeur si la condition est vraie, sinon la clé est omise. */
    protected function when(mixed $condition, mixed $value): mixed
    {
        return $condition ? value($value) : new MissingValue;
    }

    /** La valeur si la relation est déjà chargée, sinon la clé est omise (jamais de requête). */
    protected function whenLoaded(string $relation, Closure $value): mixed
    {
        return $this->resource->relationLoaded($relation) ? $value() : new MissingValue;
    }

    private static function clean(array $data): array
    {
        $out = [];
        foreach ($data as $key => $value) {
            if ($value instanceof MissingValue) {
                continue;
            }
            if ($value instanceof self || $value instanceof ViewModelCollection) {
                $value = $value->toArray();
            } elseif (is_array($value)) {
                $value = self::clean($value);
            }
            $out[$key] = $value;
        }

        return $out;
    }

    public function __get(string $name): mixed
    {
        return $this->resource->{$name};
    }

    public function __isset(string $name): bool
    {
        return isset($this->resource->{$name});
    }

    public function __call(string $method, array $arguments): mixed
    {
        return $this->resource->{$method}(...$arguments);
    }
}
