<?php

use App\_Backoffice\Plant\PlantController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->group(function () {
    Route::get('plantes', [PlantController::class, 'index'])->name('plants.index');
    Route::post('plantes', [PlantController::class, 'store'])->name('plants.store');
    Route::post('plantes/{plant}/image', [PlantController::class, 'image'])->whereNumber('plant')->name('plants.image');
});
