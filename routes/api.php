<?php

use App\Http\Controllers\ModuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('modules')
    ->name('modules.')
    ->controller(ModuleController::class)
    ->group(function () {
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/download', 'download')->name('download');
    });
