<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemandController;

Route::post('/demands', [DemandController::class, 'store']);

Route::get('/demands', function () {
    return \App\Models\Demand::latest()->get();
});

Route::post('/demands/{id}/ignore', [DemandController::class, 'ignore']);
Route::post('/demands/{id}/assume', [DemandController::class, 'assume']);