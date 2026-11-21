<?php

// Écran membre (« / » après connexion).
return [
    'nav' => [
        'current_bids' => 'This session\'s auctions',
        'unsettled' => 'Unsettled auctions',
        'inbox' => 'Received messages',
        'outbox' => 'Sent messages',
        'profile' => 'Edit personal information',
        'grade' => 'Grade change',
    ],
    // ⓖ ⓜ ⓟ ⓐ à côté de l'identifiant.
    'grades' => [
        'G' => 'General member',
        'M' => 'Regular member',
        'P' => 'Premium member',
        'A' => 'Administrator',
    ],
    'card' => [
        'lot' => 'Lot :n',
        'current_price' => 'Current price',
        'starting_price' => 'Starting price',
        'price_hidden' => 'Price shown :h h before closing',
        'ended' => 'Ended',
        'closing' => 'Closes :date KST',
        'bidders' => 'Bidders',
        'sold' => 'Sold',
        'secret_sr' => 'Price not shown on the card',
        'shh' => [
            // Petits mots de la bulle du cactus : un par carte, dans l'ordre, la liste se répète.
            'Shh!',
            'Hush!',
            'Mystery!',
            'Guess!',
            'No peeking!',
            'Secret!',
        ],
        'awarded' => 'Winning price',
    ],
    'lots' => '{1} :n lot|[2,*] :n lots',
    'empty' => 'No lots in this session yet.',
    'send_message' => 'Send a message (regular membership request, registration, etc.)',
    'send_message_hint' => 'Pick a premium member to write to.',
    'no_premium' => 'No premium members yet.',
];
