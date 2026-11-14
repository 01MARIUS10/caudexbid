<?php

use App\_Site\Home\HomeController;
use App\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::middleware(SetLocale::ALIAS)->get('/', HomeController::class)->name('home');
Route::get('lang/{locale}', [HomeController::class, 'locale'])->whereIn('locale', array_keys(SetLocale::LOCALES))->name('locale');
