<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoffeeSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'quantity',
        'price_per_kg',
        'quality_grade',
        'notes',
        'approved'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'approved' => 'boolean'
    ];

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function getTotalValueAttribute()
    {
        return $this->quantity * $this->price_per_kg;
    }

    public function getStatusAttribute()
    {
        return $this->approved ? 'Approved' : 'Pending Approval';
    }

    public function getStatusColorAttribute()
    {
        return $this->approved ? 'success' : 'warning';
    }
}
