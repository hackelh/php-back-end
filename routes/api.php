<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlbumController;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/albums', [AlbumController::class, 'index']);
    Route::get('/albums/my-albums', [AlbumController::class, 'myAlbums']);
    Route::get('/albums/{id}', [AlbumController::class, 'show']);
    Route::post('/albums', [AlbumController::class, 'store']);
    Route::put('/albums/{id}', [AlbumController::class, 'update']);
    Route::delete('/albums/{id}', [AlbumController::class, 'destroy']);
    Route::post('/albums/{id}/publish', [AlbumController::class, 'publish']);
    Route::post('/albums/{id}/unpublish', [AlbumController::class, 'unpublish']);
    Route::get('/albums/{id}/images', [AlbumController::class, 'getImages']);
    Route::get('/stats', [AlbumController::class, 'stats']);
});