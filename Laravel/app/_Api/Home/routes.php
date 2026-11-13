<?php

use App\_Api\Home\HomeController;
use Illuminate\Support\Facades\Route;

// Écran initial (utilisateur non connecté).
Route::get('home', HomeController::class);
