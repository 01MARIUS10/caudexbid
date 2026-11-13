<?php

namespace App\Shared\ViewModels;

use Countable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\HtmlString;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

/**
 * Liste de ViewModel (avec pagination éventuelle) : tableau pour Blade, JSON {data, links, meta} pour l'API.
 *
 * @implements IteratorAggregate<int, ViewModel>
 */
final class ViewModelCollection implements Countable, IteratorAggregate, JsonSerializable, Responsable
{
    /** @param list<ViewModel> $items */
    public function __construct(private readonly array $items, private readonly ?AbstractPaginator $paginator = null) {}

    /** @return list<array<string, mixed>> */
    public function toArray(): array
    {
        return array_map(fn (ViewModel $item) => $item->toArray(), $this->items);
    }

    /** Liens de pagination pour Blade (vide sans pagination). */
    public function links(): HtmlString
    {
        return new HtmlString($this->paginator ? (string) $this->paginator->links() : '');
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function toResponse($request): JsonResponse
    {
        $body = ['data' => $this->toArray()];

        if ($this->paginator instanceof LengthAwarePaginator) {
            $p = $this->paginator;
            $body['links'] = ['first' => $p->url(1), 'last' => $p->url($p->lastPage()), 'prev' => $p->previousPageUrl(), 'next' => $p->nextPageUrl()];
            $body['meta'] = [
                'current_page' => $p->currentPage(), 'from' => $p->firstItem(), 'last_page' => $p->lastPage(),
                'path' => $p->path(), 'per_page' => $p->perPage(), 'to' => $p->lastItem(), 'total' => $p->total(),
            ];
        }

        return response()->json($body);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
