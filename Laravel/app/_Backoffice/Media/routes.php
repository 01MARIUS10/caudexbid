<?php

use App\_Backoffice\Media\MediaController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->group(function () {
    Route::post('produits/{product}/medias', [MediaController::class, 'store'])->whereNumber('product')->name('products.media.store');
    Route::delete('medias/{media}', [MediaController::class, 'destroy'])->whereNumber('media')->name('media.destroy');
});
