<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    // Récupérer toutes les images
    public function getAll()
    {
        $images = Image::all();
        return response()->json($images);
    }

    // Récupérer une image spécifique
    public function get($id)
    {
        $image = Image::find($id);

        if (!$image) {
            return response()->json(['message' => 'Image non trouvée'], 404);
        }

        return response()->json($image);
    }

    // Ajouter une nouvelle image
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|file|image|max:2048',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'album_id' => 'nullable|exists:albums,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imageFile = $request->file('image');
        $imagePath = $imageFile->store('images', 'public');

        $image = Image::create([
            'album_id' => $request->album_id,
            'url' => 'http://localhost:8000/api/storage/images/' . basename($imagePath),
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json($image, 201);
    }

    // Mettre à jour une image
    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $image = Image::find($id);

        if (!$image) {
            return response()->json(['message' => 'Image non trouvée'], 404);
        }

        $image->update($request->only(['title', 'description']));

        return response()->json($image);
    }

    // Supprimer une image
    public function delete($id)
    {
        $image = Image::find($id);

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
