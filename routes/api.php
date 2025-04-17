<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

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
   ROUTES PUBLIQUES
   =========================== */
// Auth
Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Albums publics
Route::get('/albums', [AlbumController::class, 'index']);
Route::get('/stats', [StatsController::class, 'getStats']);

// Images publiques
Route::get('/storage/images/{filename}', function ($filename) {
    $path = storage_path('app/public/images/' . $filename);
    
    if (!file_exists($path)) {
        \Log::error('Image not found: ' . $path);
        return response()->json(['error' => 'Image not found'], 404);
    }
    
    try {
        return response()->file($path, [
            'Cache-Control' => 'public, max-age=31536000',
            'Access-Control-Allow-Origin' => '*',
        ]);
    } catch (\Exception $e) {
        \Log::error('Error serving image: ' . $e->getMessage());
        return response()->json(['error' => 'Error serving image'], 500);
    }
});

/* ===========================
   ROUTES PROTÉGÉES
   =========================== */
Route::middleware('auth:sanctum')->group(function () {
    // Routes des albums
    Route::get('/albums/my-albums', [AlbumController::class, 'myAlbums']);
    Route::get('/albums/{id}', [AlbumController::class, 'show']);
    Route::post('/albums', [AlbumController::class, 'store']);
    Route::put('/albums/{id}', [AlbumController::class, 'update']);
    Route::delete('/albums/{id}', [AlbumController::class, 'destroy']);
    Route::post('/albums/{id}/publish', [AlbumController::class, 'publish']);
    Route::post('/albums/{id}/unpublish', [AlbumController::class, 'unpublish']);
    Route::get('/albums/{id}/images', [AlbumController::class, 'getImages']);

    // Routes des images
    Route::get('/images', [ImageController::class, 'getAll']);
    Route::get('/images/{id}', [ImageController::class, 'get']);
    Route::post('/images', [ImageController::class, 'create']);
    Route::put('/images/{id}', [ImageController::class, 'update']);
    Route::delete('/images/{id}', [ImageController::class, 'delete']);

    // Routes d'authentification et autres
    Route::post('/logout', [AuthController::class, 'logout']); // Déconnexion
    Route::get('/user', function (Request $request) {
        return $request->user(); // Récupérer les infos de l'utilisateur connecté
    });
    Route::get('/albums/{albumId}/images', [ImageController::class, 'index']);
Route::get('/images/{imageId}', [ImageController::class, 'show']);

    // Route des statistiques
    Route::get('/stats', [AlbumController::class, 'stats']);
});


