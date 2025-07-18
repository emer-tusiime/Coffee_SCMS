<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerInventoryAlert extends Model
{
    protected $fillable = [
        'retailer_id',
        'product_id',
        'threshold',
        'is_active',
        'last_checked_at',
        'alert_frequency',
        'alert_method',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_checked_at' => 'datetime',
        'threshold' => 'integer',
        'alert_frequency' => 'integer'
    ];

    public function retailer()
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBelowThreshold($query)
    {
        return $query->whereRaw('current_stock < threshold');
    }

    public function getAlertFrequencyOptions()
    {
        return [
            1 => 'Daily',
            7 => 'Weekly',
            30 => 'Monthly'
        ];
    }

    public function getAlertMethodOptions()
    {
        return [
            'email' => 'Email',
            'sms' => 'SMS',
            'both' => 'Both'
        ];
    }
}
