<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'alert_type',
        'message',
        'status',
        'threshold',
        'current_level'
    ];

    protected $casts = [
        'threshold' => 'float',
        'current_level' => 'float',
    ];

    // Relationship with inventory item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
