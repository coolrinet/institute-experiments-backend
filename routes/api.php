<?php

use App\Http\Controllers\MachineryController;
use App\Http\Controllers\MachineryParameterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('machineries', MachineryController::class);

    Route::apiResource('machinery-parameters', MachineryParameterController::class);
});
