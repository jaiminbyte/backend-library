<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationRoom extends Model
{
    use HasFactory;

    protected $table = 'organization_rooms';

    protected $fillable = [
        'name',
        'organization_id',
        'description',
        'price_per_hour',
        'facilities',
        'images',
    ];
}
