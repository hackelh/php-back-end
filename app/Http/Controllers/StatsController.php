<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function getStats()
    {
        return [
            'total' => Album::count(),
            'public' => Album::where('isPublic', true)->count(),
            'private' => Album::where('isPublic', false)->count(),
        ];
    }
}
