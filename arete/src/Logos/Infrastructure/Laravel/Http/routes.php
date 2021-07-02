<?php

use Arete\Logos\Infrastructure\Laravel\Http\Controllers\SourceController;
use Illuminate\Support\Facades\Route;

// use Illuminate\Http\Request;

Route::group([
    'prefix' => 'test',
    'middleware' => ['web']
], function () {
    Route::group([
        'prefix' => 'sources'
    ], function () {
        Route::get('/', [SourceController::class, 'index']);
        Route::get('filter', [SourceController::class, 'filter']);
    });
});
