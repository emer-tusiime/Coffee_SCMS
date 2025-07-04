<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'status',
    ];

    // Relationships
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}