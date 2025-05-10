<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemandController;
use App\Http\Controllers\KafkaTopicController;

Route::post('/demands', [DemandController::class, 'store']);

