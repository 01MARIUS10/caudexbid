<?php

use App\_Backoffice\Member\MemberController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->group(function () {
    Route::get('membres', [MemberController::class, 'index'])->name('members.index');
    Route::get('membres/nouveau', [MemberController::class, 'create'])->name('members.create');
    Route::post('membres', [MemberController::class, 'store'])->name('members.store');
    Route::get('membres/{member}', [MemberController::class, 'show'])->whereNumber('member')->name('members.show');
});
