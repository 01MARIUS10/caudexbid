<?php

use App\_Backoffice\Bid\BidController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->get('encheres', [BidController::class, 'index'])->name('bids.index');
