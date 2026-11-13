<?php

namespace App\Shared\Support;

use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersAction;
use App\Modules\User\_UserAuth\Actions\ListMembers\ListMembersInput;
use App\Modules\User\_UserAuth\Models\UserAuth;
use Illuminate\Support\Collection;

/**
 * Annuaire des membres cités par un écran (expéditeur, enchérisseur, adjudicataire…).
 *
 * Le lien entre deux modules est une clé étrangère : le controller charge les membres UNE fois, par l'Action
 * ListMembersAction du module User, puis remet l'annuaire aux ViewModels qui n'ont plus qu'à lire.
 */
final class MemberDirectory
{
    /** @param Collection<int, UserAuth> $members membres indexés par id (profil chargé) */
    public function __construct(private readonly Collection $members) {}

    public static function empty(): self
    {
        return new self(collect());
    }

    /** @param iterable<int|null> $ids identifiants de membres à charger (doublons et null ignorés) */
    public static function load(ListMembersAction $listMembers, iterable $ids): self
    {
        $ids = collect($ids)->filter()->unique()->values()->all();

        return $ids === []
            ? self::empty()
            : new self($listMembers->execute(new ListMembersInput(ids: $ids))->keyBy('id'));
    }

    /**
     * Annuaire des membres portés par des colonnes d'éléments (ex. expediteur_id, destinataire_id).
     *
     * @param iterable<object> $items
     */
    public static function forColumns(ListMembersAction $listMembers, iterable $items, string ...$columns): self
    {
        $ids = [];
        foreach ($items as $item) {
            foreach ($columns as $column) {
                $ids[] = $item->{$column};
            }
        }

        return self::load($listMembers, $ids);
    }

    public function identifiant(?int $id): ?string
    {
        return $id ? $this->members->get($id)?->identifiant : null;
    }

    /** Identifiant du parrain (premium) du membre. */
    public function parrainId(?int $id): ?int
    {
        return $id ? $this->members->get($id)?->profile?->parrain_id : null;
    }
}
