<?php

namespace Database\Seeders;

use App\Modules\Message\Models\Enums\MessageType;
use App\Modules\Message\Models\Message;
use App\Modules\Product\Actions\NotifyAward\NotifyAwardAction;
use App\Modules\Product\Actions\NotifyAward\NotifyAwardInput;
use App\Modules\Product\Models\Enums\ProductStatus;
use App\Modules\Product\Models\Product;
use App\Modules\User\_UserAuth\Models\UserAuth;
use Illuminate\Database\Seeder;

/**
 * Demandes de statut de membre régulier + messages divers + notifications d'adjudication.
 */
class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $id = fn (string $identifiant) => UserAuth::where('identifiant', $identifiant)->value('id');

        $messages = [
            ['chen', 'andy88', MessageType::RegularMemberRequest, 'Bonjour, je souhaite devenir membre régulier pour participer aux enchères.', false],
            ['hiro', 'mika_p', MessageType::RegularMemberRequest, 'Bonjour, pourriez-vous valider mon statut de membre régulier ?', true],
            ['sora', 'mika_p', MessageType::Registration, 'Merci pour le parrainage, mes coordonnées sont à jour.', true],
            ['kenji', 'mika_p', MessageType::General, 'Le lot remporté peut-il être livré à Osaka ?', false],
            ['mika_p', 'kenji', MessageType::General, 'Oui, livraison prévue sous 10 jours après l\'acompte.', false],
        ];

        foreach ($messages as $i => [$from, $to, $type, $memo, $read]) {
            Message::create([
                'expediteur_id' => $id($from),
                'destinataire_id' => $id($to),
                'type' => $type,
                'memo' => $memo,
                'envoye_le' => now()->subHours(48 - $i * 6),
                'lu_le' => $read ? now()->subHours(40 - $i * 6) : null,
            ]);
        }

        // Notifications d'adjudication des lots de la session précédente (adjudicataire + parrain).
        $notify = app(NotifyAwardAction::class);
        Product::whereIn('statut', [ProductStatus::Awarded, ProductStatus::Sold])
            ->pluck('id')
            ->each(fn (int $produitId) => $notify->execute(new NotifyAwardInput($produitId)));
    }
}
