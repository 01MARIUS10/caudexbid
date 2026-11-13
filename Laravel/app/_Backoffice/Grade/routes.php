<?php

use App\_Backoffice\Grade\GradeController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->group(function () {
    Route::get('grades', [GradeController::class, 'index'])->name('grades.index');
    Route::post('membres/{member}/grade', [GradeController::class, 'change'])->whereNumber('member')->name('members.grade.change');
});
