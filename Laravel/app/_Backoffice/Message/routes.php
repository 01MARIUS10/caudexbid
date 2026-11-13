<?php

use App\_Backoffice\Message\MessageController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->group(function () {
    Route::get('messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('messages/nouveau', [MessageController::class, 'create'])->name('messages.create');
    Route::post('messages', [MessageController::class, 'store'])->name('messages.store');
    Route::patch('messages/{message}/lu', [MessageController::class, 'markRead'])->whereNumber('message')->name('messages.read');
});
