<?php

use App\_Api\ProductMedia\ProductMediaController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', EnsureGrade::for(GradeCode::Admin)])->group(function () {
    Route::post('products/{product}/media', [ProductMediaController::class, 'store'])->whereNumber('product');
    Route::delete('product-media/{media}', [ProductMediaController::class, 'destroy'])->whereNumber('media');
});
