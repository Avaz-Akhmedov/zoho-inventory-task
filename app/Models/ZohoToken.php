<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZohoToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'expires_at',
    ];
}
