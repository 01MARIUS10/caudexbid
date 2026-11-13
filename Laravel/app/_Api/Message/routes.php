<?php

use App\_Api\Message\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('messages')->group(function () {
    Route::get('inbox', [MessageController::class, 'inbox']);
    Route::get('outbox', [MessageController::class, 'outbox']);
    Route::get('premiums', [MessageController::class, 'premiums']);
    Route::post('/', [MessageController::class, 'store']);
    Route::patch('{message}/read', [MessageController::class, 'markRead'])->whereNumber('message');
});
