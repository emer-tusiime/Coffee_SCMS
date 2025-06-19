<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'unit',
        'current_stock',
        'minimum_stock',
        'status'
    ];

    protected $casts = [
        'current_stock' => 'float',
        'minimum_stock' => 'float',
    ];

    // Relationship with inventory alerts
    public function alerts()
    {
        return $this->hasMany(InventoryAlert::class);
    }
}
