<?php

namespace App\Modules\Message\ViewModels\BackOffice;

use App\Modules\Message\Models\Message;
use App\Shared\Support\MemberDirectory;
use App\Shared\ViewModels\ViewModel;

/** @mixin \App\Modules\Message\Models\Message */
class MessageBoViewModel extends ViewModel
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
            'expediteur_id' => $this->expediteur_id,
            'expediteur' => $this->members->identifiant($this->expediteur_id),
            'destinataire_id' => $this->destinataire_id,
            'destinataire' => $this->members->identifiant($this->destinataire_id),
            'memo' => $this->memo,
            'envoye_le' => $this->envoye_le,
            'lu_le' => $this->lu_le,
        ];
    }
}
