<?php

// Écran membre (« / » après connexion).
return [
    'nav' => [
        'current_bids' => 'Enchères de cette session',
        'unsettled' => 'Enchères non soldées',
        'inbox' => 'Messages reçus',
        'outbox' => 'Messages envoyés',
        'profile' => 'Modification des informations personnelles',
        'grade' => 'Changement de grade',
    ],
    // ⓖ ⓜ ⓟ ⓐ à côté de l'identifiant.
    'grades' => [
        'G' => 'Membre général',
        'M' => 'Membre régulier',
        'P' => 'Membre premium',
        'A' => 'Administrateur',
    ],
    'card' => [
        'lot' => 'Lot :n',
        'current_price' => 'Prix actuel',
        'starting_price' => 'Mise de départ',
        'price_hidden' => 'Prix publié :h h avant la clôture',
        'ended' => 'Terminée',
        'closing' => 'Clôture : :date KST',
        'bidders' => 'Enchérisseurs',
        'sold' => 'Vendu',
        'awarded' => 'Prix d\'adjudication',
    ],
    'lots' => '{1} :n lot|[2,*] :n lots',
    'empty' => 'Aucun lot pour cette session pour le moment.',
    'send_message' => 'Envoyer un message (demande de statut de membre régulier, enregistrement, etc.)',
    'send_message_hint' => 'Choisissez un membre premium pour lui écrire.',
    'no_premium' => 'Aucun membre premium pour le moment.',
];
