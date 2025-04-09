<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlbumController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ce fichier définit toutes les routes de l'API. Il inclut des routes 
| publiques et protégées, nécessitant une authentification via Sanctum.
|
*/

/* ===========================
   AUTHENTIFICATION (PUBLIC)
   =========================== */
Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'register']); // Inscription
    Route::post('/login', [AuthController::class, 'login']); // Connexion
});



// Routes nécessitant une authentification
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


/* ===========================
   AUTHENTIFICATION (PROTÉGÉE)
   =========================== */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']); // Déconnexion
    Route::get('/user', function (Request $request) {
        return $request->user(); // Récupérer les infos de l'utilisateur connecté
    });
});


