<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'capacity',
        'processing_type',
        'quality_standard',
        'status',
        'contact_person',
        'contact_email',
        'contact_phone',
        'operating_hours',
        'certifications',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'status' => 'boolean',
        'operating_hours' => 'array',
        'certifications' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'factory_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(User::class, 'supplier_factory', 'factory_id', 'supplier_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByCapacity($query, $min, $max = null)
    {
        if ($max) {
            return $query->whereBetween('capacity', [$min, $max]);
        }
        return $query->where('capacity', '>=', $min);
    }
}
