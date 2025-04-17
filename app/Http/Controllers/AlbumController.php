<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{
    // GET /albums
    public function index()
    {
        $albums = Album::all();
        return response()->json($albums);
    }

    // GET /albums/my-albums
    public function myAlbums()
    {
        return Album::where('user_id', Auth::id())->get();
    }

    // GET /albums/{id}
    public function show($id)
    {
        return Album::findOrFail($id);
    }

    // POST /albums
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'isPublic' => 'required|boolean',
        ]);

        $album = Album::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'isPublic' => $request->isPublic,
        ]);

        return response()->json($album, 201);
    }

    // PUT /albums/{id}
    public function update(Request $request, $id)
    {
        $album = Album::findOrFail($id);
        
        // Vérifier si l'utilisateur est le propriétaire de l'album
        if ($album->user_id !== Auth::id()) {
            return response()->json(['message' => 'Non autorisé à modifier cet album'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'isPublic' => 'sometimes|required|boolean',
        ]);

        $album->update($request->only('title', 'description', 'isPublic'));

        return $album;
    }

    // DELETE /albums/{id}
    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        
        // Vérifier si l'utilisateur est le propriétaire de l'album
        if ($album->user_id !== Auth::id()) {
            return response()->json(['message' => 'Non autorisé à supprimer cet album'], 403);
        }

        // Supprimer les images associées à l'album
        foreach ($album->images as $image) {
            if ($image->url) {
                $path = str_replace('storage/', 'public/', $image->url);
                Storage::delete($path);
            }
            $image->delete();
        }

        $album->delete();
        return response()->json(null, 204);
    }

    // POST /albums/{id}/publish
    public function publish($id)
    {
        $album = Album::findOrFail($id);
        $album->isPublic = true;
        $album->save();

        return $album;
    }

    // POST /albums/{id}/unpublish
    public function unpublish($id)
    {
        $album = Album::findOrFail($id);
        $album->isPublic = false;
        $album->save();

        return $album;
    }

    // GET /albums/{id}/images
    public function getImages($id)
    {
        $album = Album::findOrFail($id);
        return $album->images;
    }

    // POST /albums/{id}/images (upload image)
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|max:2048', // max 2 Mo
        ]);

        $album = Album::findOrFail($id);

        if ($album->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $path = $request->file('image')->store('images', 'public');

        $image = Image::create([
            'album_id' => $album->id,
            'path' => $path,
        ]);

        return response()->json($image, 201);
    }

    // GET /stats
    public function stats()
    {
        return [
            'total_albums' => Album::count(),
            'albums_public' => Album::where('isPublic', true)->count(),
            'albums_private' => Album::where('isPublic', false)->count(),
            'total_images' => Image::count(), // décommenté pour les stats
        ];
    }
}
