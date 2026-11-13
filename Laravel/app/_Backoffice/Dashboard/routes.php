<?php

use App\_Backoffice\Dashboard\DashboardController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->get('/', DashboardController::class)->name('dashboard');
