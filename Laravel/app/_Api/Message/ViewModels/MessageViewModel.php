<?php

namespace App\_Api\Message\ViewModels;

use App\Modules\Message\Models\Message;
use App\Shared\Support\MemberDirectory;
use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\Message\Models\Message */
class MessageViewModel extends ViewModel
{
    public function __construct(Message $resource, protected readonly MemberDirectory $members)
    {
        parent::__construct($resource);
    }

    protected function present(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'expediteur' => $this->members->identifiant($this->expediteur_id),
            'destinataire' => $this->members->identifiant($this->destinataire_id),
            'memo' => $this->memo,
            'envoye_le' => $this->envoye_le,
            'lu_le' => $this->lu_le,
        ];
    }
}
