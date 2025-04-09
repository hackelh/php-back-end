<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->authorize('update', $album); // facultatif

        $album->update($request->only('title', 'description', 'isPublic'));

        return $album;
    }

    // DELETE /albums/{id}
    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        $this->authorize('delete', $album); // facultatif
        $album->delete();

        return response()->json(null, 204);
    }

    // POST /albums/{id}/publish
    public function publish($id)
    {
        $album = Album::findOrFail($id);
        $album->is_public = true;
        $album->save();

        return $album;
    }

    // POST /albums/{id}/unpublish
    public function unpublish($id)
    {
        $album = Album::findOrFail($id);
        $album->is_public = false;
        $album->save();

        return $album;
    }

    // GET /albums/{id}/images
    public function getImages($id)
    {
        $album = Album::with('images')->findOrFail($id); // à condition que tu aies un modèle Image
        return $album->images;
    }

    // GET /stats
    public function stats()
    {
        return [
            'total' => Album::count(),
            'public' => Album::where('isPublic', true)->count(),
            'private' => Album::where('isPublic', false)->count(),
        ];
    }

}

