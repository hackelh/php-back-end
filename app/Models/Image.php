<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'album_id',
        'url',
        'title',
        'description',
        'download_count'
    ];

    protected $appends = ['filePath'];

    // Relation : une image appartient à un album
    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    // Accesseur pour filePath
    public function getFilePathAttribute()
    {
        return $this->url ? URL::to($this->url) : null;
    }
}
