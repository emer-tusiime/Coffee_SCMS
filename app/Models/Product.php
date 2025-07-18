<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'supplier_id',
        'factory_id',
        'status',
        'price',
        'stock',
        'type', // 'raw' or 'processed'
        'quality_grade',
        'units',
        'min_order_quantity',
        'max_order_quantity',
        'lead_time_days',
        'last_updated_at',
        'threshold',
    ];

    protected $casts = [
        'last_updated_at' => 'datetime',
        'status' => 'boolean',
        'price' => 'decimal:2',
        'stock' => 'integer',
        'min_order_quantity' => 'integer',
        'max_order_quantity' => 'integer',
        'lead_time_days' => 'integer',
        'threshold' => 'integer',
    ];

    protected $appends = ['is_raw', 'is_processed'];

    public function getIsRawAttribute()
    {
        return $this->type === 'raw';
    }

    public function getIsProcessedAttribute()
    {
        return $this->type === 'processed';
    }

    public function scopeRaw($query)
    {
        return $query->where('type', 'raw');
    }

    public function scopeProcessed($query)
    {
        return $query->where('type', 'processed');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', true);
    }

    public function scopeSupplierProducts($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeFactoryProducts($query, $factoryId)
    {
        return $query->where('factory_id', $factoryId);
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function factory()
    {
        return $this->belongsTo(\App\Models\Factory::class, 'factory_id');
    }

    public function wholesalerPrices()
    {
        return $this->hasMany(\App\Models\WholesalerProductPrice::class, 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'product_id');
    }

    public function getWholesalerPrice($wholesalerId)
    {
        $price = $this->wholesalerPrices()->where('wholesaler_id', $wholesalerId)->first();
        return $price ? $price->price : null;
    }
}
