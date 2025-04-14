<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function index()
    {
        return Image::all();
    }

    public function show($id)
    {
        return Image::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
            'album_id' => 'required|exists:albums,id',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Stockage du fichier
            $path = $file->storeAs('public/images', $filename);
            
            // Création de l'enregistrement dans la base de données
            $image = new Image([
                'album_id' => $request->album_id,
                'url' => '/storage/images/' . $filename,
                'title' => $request->title ?? $file->getClientOriginalName(),
                'description' => $request->description ?? '',
                'download_count' => 0
            ]);

            $image->save();

            // Forcer le chargement des accesseurs
            $image->refresh();

            return response()->json($image, 201);
        }

        return response()->json(['error' => 'Aucun fichier n\'a été envoyé.'], 400);
    }

    public function update(Request $request, $id)
    {
        $image = Image::findOrFail($id);
        
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
        ]);

        $image->update($request->only(['title', 'description']));
        return $image;
    }

    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        
        // Suppression du fichier physique
        if ($image->url) {
            $path = str_replace('/storage/', 'public/', $image->url);
            Storage::delete($path);
        }
        
        $image->delete();
        return response()->json(null, 204);
    }
}
