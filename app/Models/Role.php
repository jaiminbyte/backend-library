<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'base_currencies';

    protected $fillable = [
        'user_id', 'module_id', 'admin', 'manager', 'sales', 'customer', 'portal'
    ];
}
