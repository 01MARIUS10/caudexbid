<?php

use App\_Api\Profil\ProfilController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('me')->group(function () {
    Route::get('profil', [ProfilController::class, 'show']);
    Route::patch('profil', [ProfilController::class, 'update']);
    Route::get('preferences', [ProfilController::class, 'showPreferences']);
    Route::patch('preferences', [ProfilController::class, 'updatePreferences']);
});
