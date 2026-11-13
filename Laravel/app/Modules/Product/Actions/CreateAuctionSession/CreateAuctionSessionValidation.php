<?php

namespace App\Modules\Product\Actions\CreateAuctionSession;

use App\Modules\User\_UserAuth\Models\UserAuth;
use App\Shared\Exceptions\BusinessRuleException;
use Illuminate\Support\Facades\Validator;

/**
 * Période d'enchères : durée fixée de 3 à 5 jours.
 * Heure de clôture conseillée : dimanche soir 20h-22h (KST) — non bloquant.
 */
final class CreateAuctionSessionValidation
{
    public function rules(): array
    {
        return [
            'titre' => ['nullable', 'string', 'max:100'],
            'debut_le' => ['required', 'date'],
            'cloture_le' => ['required', 'date', 'after:debut_le'],
        ];
    }

    public function validate(array $data, UserAuth $actor): CreateAuctionSessionInput
    {
        if (! $actor->isAdmin()) {
            throw BusinessRuleException::forbidden();
        }

        $input = CreateAuctionSessionInput::fromArray(Validator::make($data, $this->rules())->validate());

        $hours = $input->debutLe->diffInHours($input->clotureLe);
        $min = config('caudexbid.auction_min_days') * 24;
        $max = config('caudexbid.auction_max_days') * 24;

        if ($hours < $min || $hours > $max) {
            throw new BusinessRuleException(sprintf(
                'La durée de l\'enchère doit être comprise entre %d et %d jours.',
                config('caudexbid.auction_min_days'),
                config('caudexbid.auction_max_days'),
            ));
        }

        return $input;
    }
}
