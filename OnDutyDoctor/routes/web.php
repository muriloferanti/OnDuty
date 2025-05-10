<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemandController;

Route::get('/demands', [DemandController::class, 'index']);

