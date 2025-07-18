<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'alert_type',
        'message',
        'status',
        'threshold',
        'current_level',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'threshold' => 'float',
        'current_level' => 'float',
        'status' => 'boolean'
    ];

    // Relationship with product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
