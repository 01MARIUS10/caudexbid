<?php

use App\_Backoffice\Owner\OwnerController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->group(function () {
    Route::get('proprietaires', [OwnerController::class, 'index'])->name('owners.index');
    Route::post('proprietaires', [OwnerController::class, 'store'])->name('owners.store');
});
