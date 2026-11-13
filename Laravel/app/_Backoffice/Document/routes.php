<?php

use App\_Backoffice\Document\DocumentController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->group(function () {
    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('documents/{numero}', [DocumentController::class, 'show'])->whereNumber('numero')->name('documents.show');
});
