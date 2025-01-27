<?php

use App\Http\Controllers\ModuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(ModuleController::class)
    ->group(function () {
        Route::post('/modules', 'store');
        Route::get('/modules/{id}/download', 'download');
    });
