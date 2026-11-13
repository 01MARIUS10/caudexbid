<?php

use App\_Backoffice\Product\ProductController;
use App\Middleware\EnsureGrade;
use App\Modules\User\_UserGrad\Models\Enums\GradeCode;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureGrade::for(GradeCode::Admin)])->group(function () {
    Route::get('produits', [ProductController::class, 'index'])->name('products.index');
    Route::get('produits/nouveau', [ProductController::class, 'create'])->name('products.create');
    Route::post('produits', [ProductController::class, 'store'])->name('products.store');
    Route::get('produits/{product}', [ProductController::class, 'show'])->whereNumber('product')->name('products.show');
    Route::post('produits/{product}/reglement', [ProductController::class, 'settle'])->whereNumber('product')->name('products.settle');
    Route::post('encheres/cloture', [ProductController::class, 'closeAuctions'])->name('auctions.close');
});
