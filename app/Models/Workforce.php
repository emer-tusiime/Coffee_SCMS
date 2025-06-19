<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workforce extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'contact_info',
        'status',
    ];
}