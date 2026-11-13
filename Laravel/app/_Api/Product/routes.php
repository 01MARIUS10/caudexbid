<?php

use App\_Api\Product\ProductController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/unsettled', [ProductController::class, 'unsettled']);
    Route::get('products/{product}', [ProductController::class, 'show'])->whereNumber('product');

    // Administration.
    Route::middleware(EnsureGrade::for(GradeCode::Admin))->group(function () {
        Route::post('products', [ProductController::class, 'store']);
        Route::post('products/{product}/settle', [ProductController::class, 'settle'])->whereNumber('product');
    });
});
