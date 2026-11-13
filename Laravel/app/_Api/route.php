<?php

/*
|--------------------------------------------------------------------------
| Routes API (préfixe /api)
|--------------------------------------------------------------------------
| Regroupe les routes de chaque dossier ({Route}/routes.php).
*/
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json([
    'application' => 'caudexbid API',
    'auth' => url('/api/auth/login'),
    'back_office' => url('/backoffice'),
]));

require __DIR__.'/Achat/routes.php';
require __DIR__.'/AuctionSession/routes.php';
require __DIR__.'/Auth/routes.php';
require __DIR__.'/Bid/routes.php';
require __DIR__.'/Document/routes.php';
require __DIR__.'/Grade/routes.php';
require __DIR__.'/Home/routes.php';
require __DIR__.'/Message/routes.php';
require __DIR__.'/Product/routes.php';
require __DIR__.'/ProductMedia/routes.php';
require __DIR__.'/ProductOwner/routes.php';
require __DIR__.'/Profil/routes.php';
