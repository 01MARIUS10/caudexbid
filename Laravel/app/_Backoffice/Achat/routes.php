<?php

use App\_Backoffice\Achat\AchatController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->put('produits/{product}/achat', [AchatController::class, 'define'])->whereNumber('product')->name('products.achat.define');
