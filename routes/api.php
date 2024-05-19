<?php

use App\Http\Controllers\ExperimentController;
use App\Http\Controllers\MachineryController;
use App\Http\Controllers\MachineryParameterController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', function (Request $request) {
        return UserResource::make($request->user());
    });

    Route::apiResource('machineries', MachineryController::class);

    Route::apiResource('machinery-parameters', MachineryParameterController::class);

    Route::apiResource('research', ResearchController::class);

    Route::apiResource('research.experiments', ExperimentController::class);

    Route::apiResource('users', UserController::class)
        ->except(['update', 'show'])
        ->middleware('is_admin');
});
