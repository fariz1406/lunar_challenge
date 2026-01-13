<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ImportController;

Route::post('/upload-csv', [ImportController::class, 'store']);