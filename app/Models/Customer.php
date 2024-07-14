<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'mobile_number', 'street', 'city', 'state', 'postal_code', 'country', 'user_name', 'password'
    ];
}
