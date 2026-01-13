<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ImportController;
use App\Http\Controllers\Api\JobStatusController;

Route::post('/upload-csv', [ImportController::class, 'store']);

Route::get('/import-status/{importId}', [JobStatusController::class, 'show']);