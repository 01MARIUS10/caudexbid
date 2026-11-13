<?php

use App\Modules\Product\Actions\CloseExpiredAuctions\CloseExpiredAuctionsAction;
use App\Modules\Product\Actions\CloseExpiredAuctions\CloseExpiredAuctionsValidation;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
| Clôture automatique : ouvre les sessions, adjuge / clôture les lots expirés (après soft close).
*/
Artisan::command('auctions:close', function (CloseExpiredAuctionsValidation $validation, CloseExpiredAuctionsAction $action) {
    $result = $action->execute($validation->validate());
    $this->info(json_encode($result));
})->purpose('Clôture les enchères dont l\'heure de clôture effective est dépassée');

Schedule::command('auctions:close')->everyMinute()->withoutOverlapping();
