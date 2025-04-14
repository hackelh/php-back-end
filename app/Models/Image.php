<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    // Champs pouvant être remplis automatiquement
    protected $fillable = ['album_id', 'url', 'download_count'];

    // Relation : une image appartient à un album
    public function album()
    {
        return $this->belongsTo(Album::class);
        
    }
}
