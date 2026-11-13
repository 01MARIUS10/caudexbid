<?php

use App\_Api\Grade\GradeController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('grades', [GradeController::class, 'index']);

    Route::middleware(EnsureGrade::for(GradeCode::Premium, GradeCode::Admin))->group(function () {
        Route::get('grade-changes/candidates', [GradeController::class, 'candidates']);
        Route::post('users/{user}/grade', [GradeController::class, 'change'])->whereNumber('user');
        Route::get('users/{user}/grades', [GradeController::class, 'history'])->whereNumber('user');
    });
});
