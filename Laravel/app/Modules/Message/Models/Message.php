<?php

namespace App\Modules\Message\Models;

use App\Modules\Message\Models\Enums\MessageType;
use Illuminate\Database\Eloquent\Model;

/**
 * Table « Message » : expéditeur, destinataire, mémo, date + heure d'envoi.
 *
 * Pas de relation vers les membres (module User) : expediteur_id / destinataire_id sont des clés étrangères ;
 * les membres sont attachés par le controller (relations « expediteur » / « destinataire ») via MemberAttacher.
 *
 * @property int $id
 * @property int $expediteur_id
 * @property int $destinataire_id
 */
class Message extends Model
{
    public $timestamps = false;

    protected $table = 'messages';

    protected $fillable = ['expediteur_id', 'destinataire_id', 'type', 'memo', 'envoye_le', 'lu_le'];

    protected function casts(): array
    {
        return [
            'type' => MessageType::class,
            'envoye_le' => 'datetime',
            'lu_le' => 'datetime',
        ];
    }
}
