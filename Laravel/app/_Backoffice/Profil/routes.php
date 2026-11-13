<?php

use App\_Backoffice\Profil\ProfilController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->group(function () {
    Route::put('membres/{member}/profil', [ProfilController::class, 'update'])->whereNumber('member')->name('members.profil.update');
    Route::put('membres/{member}/preferences', [ProfilController::class, 'updatePreferences'])->whereNumber('member')->name('members.preferences.update');
});
