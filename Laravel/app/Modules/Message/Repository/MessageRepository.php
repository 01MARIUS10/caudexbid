<?php

namespace App\Modules\Message\Repository;

use App\Modules\Message\Models\Enums\MessageType;
use App\Modules\Message\Models\Message;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MessageRepository
{
    public function find(int $id): ?Message
    {
        return Message::find($id);
    }

    public function create(array $attributes): Message
    {
        return Message::create($attributes + ['envoye_le' => now()]);
    }

    /** Messages reçus. */
    public function inbox(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Message::where('destinataire_id', $userId)->orderByDesc('envoye_le')->paginate($perPage);
    }

    /** Messages envoyés. */
    public function outbox(int $userId, int $perPage = 20): LengthAwarePaginator
    {
        return Message::where('expediteur_id', $userId)->orderByDesc('envoye_le')->paginate($perPage);
    }

    /** Tous les messages (back office), filtres : type, membre (expéditeur ou destinataire). */
    public function search(?MessageType $type = null, ?int $membreId = null, int $perPage = 25): LengthAwarePaginator
    {
        return $this->filtered($type, $membreId)->paginate($perPage)->withQueryString();
    }

    public function latest(?MessageType $type, ?int $membreId, int $limit): Collection
    {
        return $this->filtered($type, $membreId)->limit($limit)->get();
    }

    public function unreadCountFor(int $userId): int
    {
        return Message::where('destinataire_id', $userId)->whereNull('lu_le')->count();
    }

    public function markRead(Message $message): Message
    {
        if ($message->lu_le === null) {
            $message->forceFill(['lu_le' => now()])->save();
        }

        return $message;
    }

    /** Demandes de statut de membre régulier reçues par un premium (option : d'un membre précis). */
    public function memberRequests(int $premiumId, ?int $requesterId = null): Collection
    {
        return Message::where('destinataire_id', $premiumId)
            ->where('type', MessageType::RegularMemberRequest)
            ->when($requesterId, fn ($q) => $q->where('expediteur_id', $requesterId))
            ->orderByDesc('envoye_le')
            ->get();
    }

    private function filtered(?MessageType $type, ?int $membreId)
    {
        return Message::query()
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($membreId, fn ($q) => $q->where(fn ($w) => $w
                ->where('expediteur_id', $membreId)
                ->orWhere('destinataire_id', $membreId)))
            ->orderByDesc('envoye_le')
            ->orderByDesc('id');
    }
}
