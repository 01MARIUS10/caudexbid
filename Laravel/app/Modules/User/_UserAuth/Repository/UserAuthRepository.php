<?php

namespace App\Modules\User\_UserAuth\Repository;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserAuthRepository
{
    /** Relations nécessaires pour connaître le grade d'un membre. */
    private const WITH_GRADE = ['currentUserGrade.grade'];

    public function findById(int $id): ?UserAuth
    {
        return UserAuth::with(self::WITH_GRADE)->find($id);
    }

    public function findByIdentifiant(string $identifiant): ?UserAuth
    {
        return UserAuth::with(self::WITH_GRADE)->where('identifiant', $identifiant)->first();
    }

    public function create(array $attributes): UserAuth
    {
        return UserAuth::create($attributes);
    }

    public function touchLastLogin(UserAuth $user): void
    {
        $user->forceFill(['derniere_connexion_le' => now()])->save();
    }

    /** Membres dont le grade actuel est l'un des codes donnés. */
    public function withGrade(GradeCode ...$codes): Collection
    {
        return $this->queryWithGrade(...$codes)
            ->with(self::WITH_GRADE)
            ->orderBy('identifiant')
            ->get();
    }

    /** Liste des membres premium (destinataires des demandes de statut). */
    public function premiums(): Collection
    {
        return $this->withGrade(GradeCode::Premium);
    }

    public function firstAdmin(): ?UserAuth
    {
        return $this->queryWithGrade(GradeCode::Admin)->orderBy('id')->first();
    }

    public function isPremium(int $userId): bool
    {
        return $this->queryWithGrade(GradeCode::Premium)->whereKey($userId)->exists();
    }

    /**
     * Liste filtrée (ids, identifiants, grades), grade + profil + parrain chargés.
     *
     * @param  list<int>|null  $ids
     * @param  list<string>|null  $identifiants
     * @param  list<GradeCode>|null  $grades
     */
    public function list(?array $ids = null, ?array $identifiants = null, ?array $grades = null): Collection
    {
        return ($grades ? $this->queryWithGrade(...$grades) : UserAuth::query())
            ->with([...self::WITH_GRADE, 'profile.parrain', 'preference'])
            ->when($ids !== null, fn (Builder $b) => $b->whereIn('id', $ids))
            ->when($identifiants !== null, fn (Builder $b) => $b->whereIn('identifiant', $identifiants))
            ->orderBy('identifiant')
            ->get();
    }

    /** Liste paginée des membres (filtres : grade, recherche identifiant / e-mail / nom). */
    public function search(?GradeCode $grade = null, ?string $q = null, int $perPage = 25): LengthAwarePaginator
    {
        return ($grade ? $this->queryWithGrade($grade) : UserAuth::query())
            ->with([...self::WITH_GRADE, 'profile.parrain', 'preference'])
            ->when($q, fn (Builder $b) => $b->where(fn (Builder $w) => $w
                ->where('identifiant', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhereHas('profile', fn (Builder $p) => $p->where('nom', 'like', "%{$q}%"))))
            ->orderBy('identifiant')
            ->paginate($perPage)
            ->withQueryString();
    }

    /** @return array<string, int> nombre de membres par code de grade */
    public function countByGrade(): array
    {
        return collect(GradeCode::cases())
            ->mapWithKeys(fn (GradeCode $c) => [$c->value => $this->queryWithGrade($c)->count()])
            ->all();
    }

    /** Membres affichés dans une liste de choix (id => « identifiant (G) »). */
    public function options(GradeCode ...$codes): Collection
    {
        return $this->withGrade(...($codes ?: GradeCode::cases()))
            ->mapWithKeys(fn (UserAuth $u) => [$u->id => $u->headerLabel()]);
    }

    private function queryWithGrade(GradeCode ...$codes): Builder
    {
        $values = array_map(fn (GradeCode $c) => $c->value, $codes);

        return UserAuth::query()->whereHas(
            'currentUserGrade.grade',
            fn (Builder $q) => $q->whereIn('code', $values)
        );
    }
}
