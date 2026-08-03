<?php

use App\Http\Controllers\Api\V1\DisplayFeedController;
use App\Http\Controllers\Api\V1\ForexController;
use App\Http\Controllers\Api\V1\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/screens/{uuid}/feed', [DisplayFeedController::class, 'show'])
        ->name('api.screens.feed');

    Route::get('/forex', [ForexController::class, 'show'])
        ->name('api.forex');

    Route::get('/media/{path}', [MediaController::class, 'show'])
        ->where('path', '.*')
        ->name('api.media.show');
});
