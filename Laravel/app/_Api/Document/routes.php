<?php

use App\_Api\Document\DocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('documents/{numero}', [DocumentController::class, 'show'])->whereNumber('numero');
