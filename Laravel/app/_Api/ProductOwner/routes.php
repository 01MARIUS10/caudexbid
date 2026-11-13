<?php

use App\_Api\ProductOwner\ProductOwnerController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', EnsureGrade::for(GradeCode::Admin)])->group(function () {
    Route::get('product-owners', [ProductOwnerController::class, 'index']);
    Route::post('product-owners', [ProductOwnerController::class, 'store']);
});
