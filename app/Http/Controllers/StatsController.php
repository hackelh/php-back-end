<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function getStats()
    {
        return [
            'total_albums' => Album::count(),
            'albums_public' => Album::where('isPublic', true)->count(),
            'albums_private' => Album::where('isPublic', false)->count(),
            'total_images' => Image::count(),
        ];
    }

    private function getPublicStats()
    {
        $publicAlbums = Album::where('isPublic', true)->get();
        $publicAlbumIds = $publicAlbums->pluck('id');
        
        return [
            'albums' => [
                'count' => $publicAlbums->count(),
                'totalImages' => Image::whereIn('album_id', $publicAlbumIds)->count(),
                'averageImagesPerAlbum' => $publicAlbums->count() > 0 
                    ? round(Image::whereIn('album_id', $publicAlbumIds)->count() / $publicAlbums->count(), 2)
                    : 0
            ]
        ];
    }

    private function getPrivateStats()
    {
        $privateAlbums = Album::where('isPublic', false)->get();
        $privateAlbumIds = $privateAlbums->pluck('id');

        return [
            'albums' => [
                'count' => $privateAlbums->count(),
                'totalImages' => Image::whereIn('album_id', $privateAlbumIds)->count(),
                'averageImagesPerAlbum' => $privateAlbums->count() > 0
                    ? round(Image::whereIn('album_id', $privateAlbumIds)->count() / $privateAlbums->count(), 2)
                    : 0
            ]
        ];
    }
}
