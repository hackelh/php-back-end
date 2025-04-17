<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    // Récupérer les images d'un album
    public function index($albumId)
    {
        $images = Image::where('album_id', $albumId)->get();

        return response()->json($images);
    }

    // Récupérer une image spécifique
    public function show($imageId)
    {
        $image = Image::find($imageId);

        if (!$image) {
            return response()->json(['message' => 'Image non trouvée'], 404);
        }

        return response()->json($image);
    }

    // Ajouter une nouvelle image
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'album_id' => 'required|exists:albums,id',
            'image' => 'required|image|max:2048',
            'title' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imageFile = $request->file('image');
        $imagePath = $imageFile->store('images', 'public');

        $image = Image::create([
            'album_id' => $request->album_id,
            'url' => Storage::url($imagePath),
            'title' => $request->title,
        ]);

        return response()->json([
            'message' => 'Image téléchargée avec succès',
            'image' => $image,
        ], 201);
    }

    // Supprimer une image
    public function destroy($imageId)
    {
        $image = Image::find($imageId);

        if (!$image) {
            return response()->json(['message' => 'Image non trouvée'], 404);
        }

        // Supprimer l'image du stockage
        Storage::delete(str_replace('/storage', 'public', $image->url));

        // Supprimer l'image de la base de données
        $image->delete();

        return response()->json(['message' => 'Image supprimée avec succès']);
    }
}
