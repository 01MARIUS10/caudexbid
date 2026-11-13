<?php

use App\_Api\Bid\BidController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('products/{product}/bids', [BidController::class, 'store'])
        ->whereNumber('product')
        ->middleware(EnsureGrade::for(GradeCode::Regulier, GradeCode::Premium));
    Route::get('products/{product}/bids', [BidController::class, 'history'])
        ->whereNumber('product')
        ->middleware(EnsureGrade::for(GradeCode::Admin));
});
