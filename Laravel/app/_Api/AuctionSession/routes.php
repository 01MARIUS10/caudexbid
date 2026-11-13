<?php

use App\_Api\AuctionSession\AuctionSessionController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auction-sessions/ticker', [AuctionSessionController::class, 'ticker']);

    Route::middleware(EnsureGrade::for(GradeCode::Admin))->group(function () {
        Route::get('auction-sessions', [AuctionSessionController::class, 'index']);
        Route::post('auction-sessions', [AuctionSessionController::class, 'store']);
    });
});
