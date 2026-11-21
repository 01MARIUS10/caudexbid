<?php

namespace App\Modules\Product\Repository;

use App\Modules\Product\Models\Enums\AuctionSessionStatus;
use App\Modules\Product\Models\ProductAuctionSession;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class ProductAuctionSessionRepository
{
    public function find(int $id): ?ProductAuctionSession
    {
        return ProductAuctionSession::find($id);
    }

    public function all(): Collection
    {
        return ProductAuctionSession::withCount('products')->orderByDesc('debut_le')->get();
    }

    public function create(array $attributes): ProductAuctionSession
    {
        return ProductAuctionSession::create($attributes);
    }

    public function updateStatus(ProductAuctionSession $session, AuctionSessionStatus $status): void
    {
        $session->forceFill(['statut' => $status])->save();
    }

    /** Session en cours, sinon la prochaine programmée. */
    public function current(): ?ProductAuctionSession
    {
        return ProductAuctionSession::where('statut', AuctionSessionStatus::Open)->orderByDesc('debut_le')->first()
            ?? $this->next(now());
    }

    /** Session précédente (début antérieur) qui contient des lots : source par défaut de auctions:relist. */
    public function previousWithProducts(ProductAuctionSession $session): ?ProductAuctionSession
    {
        return ProductAuctionSession::where('debut_le', '<', $session->debut_le)
            ->whereHas('products')
            ->orderByDesc('debut_le')
            ->first();
    }

    /** Prochaine enchère (bandeau défilant). */
    public function next(CarbonInterface $now): ?ProductAuctionSession
    {
        return ProductAuctionSession::where('statut', AuctionSessionStatus::Scheduled)
            ->where('debut_le', '>', $now)
            ->orderBy('debut_le')
            ->first();
    }

    /** Sessions programmées dont la date de début est atteinte. */
    public function dueToOpen(CarbonInterface $now): Collection
    {
        return ProductAuctionSession::where('statut', AuctionSessionStatus::Scheduled)
            ->where('debut_le', '<=', $now)
            ->get();
    }

    /** Sessions ouvertes dont la date de clôture est dépassée. */
    public function openPastClosing(CarbonInterface $now): Collection
    {
        return ProductAuctionSession::where('statut', AuctionSessionStatus::Open)
            ->where('cloture_le', '<=', $now)
            ->get();
    }
}
