<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = ['title', 'description', 'isPublic', 'user_id'];

public function user() {
    return $this->belongsTo(User::class);
}

public function images() {
    return $this->hasMany(Image::class); // À créer plus tard pour les images
}
}
