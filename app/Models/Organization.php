<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'address',
        'image',
        'description',
        'opening_time',
        'closing_time',
        'dayoff'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        $baseUrl = env('HOST_URL');
        return $this->image ? env('HOST_URL').$this->image : null;
    }
}
