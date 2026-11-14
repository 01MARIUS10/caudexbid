<?php

/*
|--------------------------------------------------------------------------
| Règles métier caudexbid.com (cahier des charges)
|--------------------------------------------------------------------------
*/

return [

    // Informations sur l'exploitant : nom du site + e-mail uniquement (pas de téléphone).
    'exploitant' => [
        'site' => env('CAUDEXBID_SITE', 'www.caudexbid.com'),
        'email' => env('CAUDEXBID_EMAIL', 'ndwell@gmail.com'),
    ],

    // Documentation Swagger de l'API sur /docs (mettre API_DOCS_ENABLED=false pour la masquer).
    'api_docs' => env('API_DOCS_ENABLED', true),

    // Identifiant du compte admin utilisé comme expéditeur des messages système.
    'system_sender_identifiant' => env('CAUDEXBID_SYSTEM_SENDER', 'admin'),

    // Fuseau d'affichage des heures de clôture (KST).
    'display_timezone' => 'Asia/Seoul',

    // Durée d'une enchère : 3 à 5 jours.
    'auction_min_days' => 3,
    'auction_max_days' => 5,

    // Prix actuel, prochaine enchère, nb d'enchérisseurs et achat immédiat
    // ne sont publiés que pendant les X dernières heures.
    'price_visibility_hours' => 48,

    // Soft close : enchère dans les 5 dernières minutes => +5 min, 6 fois max (30 min).
    'soft_close' => [
        'window_minutes' => 5,
        'extension_minutes' => 5,
        'max_extensions' => 6,
    ],

    // Contenu multimédia : 4 photos max, 1 vidéo d'environ 15 s.
    'media' => [
        'disk' => 'public',
        'max_photos' => 4,
        'max_video_seconds' => 20,
        'showcase_max' => 20,
    ],

    // Répartition de (prix d'adjudication - prix de vente existant), en %.
    'shares' => [
        'proprietaire' => 30,
        'premium' => 30,
        'exploitant' => 40,
    ],
];
