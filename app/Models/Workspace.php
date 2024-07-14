<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;

    protected $table = 'workspaces';

    protected $fillable = [
        'name',
        'location',
        'time_from',
        'time_to',
        'city',
        'street',
        'user_id',
        'state',
        'postal_code'
    ];
}
