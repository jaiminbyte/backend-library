<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessRight extends Model
{
    use HasFactory;

    protected $table = 'access_rights';

    protected $fillable = [
        'dashboard',
        'librarian',
        'user',
        'booking',
        'books',
        'user_id',
        'googleBooks'
    ];
}
