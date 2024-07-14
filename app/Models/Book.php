<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'publisher',
        'year',
        'genre',
        'quantity',
        'image',
        'is_online_image'
    ];

    public function getImageUrlAttribute()
    {
        $baseUrl = env('HOST_URL');
        return $this->image ? env('HOST_URL').$this->image : null;
    }
}
