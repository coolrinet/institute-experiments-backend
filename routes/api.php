<?php

use App\Http\Controllers\ExperimentController;
use App\Http\Controllers\MachineryController;
use App\Http\Controllers\MachineryParameterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::name('profile.')
        ->prefix('profile')
        ->controller(ProfileController::class)
        ->group(function () {
            Route::get('/', 'showProfile')->name('show');
            Route::put('/', 'updateProfile')->name('update');
            Route::delete('/', 'deleteProfile')->name('delete');
        });

    Route::apiResource('machineries', MachineryController::class);

    Route::apiResource('machinery-parameters', MachineryParameterController::class);

    Route::apiResource('research', ResearchController::class);

    Route::apiResource('research.experiments', ExperimentController::class);

    Route::apiResource('users', UserController::class)
        ->except(['update', 'show']);
});
