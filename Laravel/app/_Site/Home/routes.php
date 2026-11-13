<?php

use App\_Site\Home\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
