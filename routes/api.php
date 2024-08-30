<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware('verified')->group(function () {
        Route::apiResource('tags', TagController::class);

        Route::get('posts/trashed', [PostController::class, 'trash']);
        Route::post('posts/restore/{id}', [PostController::class, 'restore']);
        Route::apiResource('posts', PostController::class);
    });
});


Route::get('/status', StatusController::class);
