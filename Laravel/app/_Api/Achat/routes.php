<?php

use App\_Api\Achat\AchatController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::put('products/{product}/achat', [AchatController::class, 'define'])
        ->whereNumber('product')
        ->middleware(EnsureGrade::for(GradeCode::Admin));
    Route::post('products/{product}/achat/confirm', [AchatController::class, 'buy'])
        ->whereNumber('product')
        ->middleware(EnsureGrade::for(GradeCode::Regulier, GradeCode::Premium));
});
