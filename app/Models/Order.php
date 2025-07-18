<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_type', // 'supplier' or 'retailer'
        'supplier_id',
        'factory_id',
        'retailer_id',
        'customer_id',
        'wholesaler_id',
        'total',
        'total_amount',
        'status',
        'notes',
        'delivery_date',
        'payment_status',
        'payment_method',
        'shipping_address',
        'estimated_delivery_date',
        'priority_level',
        'created_by',
        'approved_by',
        'approved_at',
        'shipped_at',
        'delivered_at',
        'order_date',
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
        'estimated_delivery_date' => 'datetime',
        'approved_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'priority_level' => 'integer',
        'payment_status' => 'boolean',
        'total' => 'decimal:2'
    ];

    protected $appends = ['is_supplier_order', 'is_retailer_order'];

    public function getIsSupplierOrderAttribute()
    {
        return $this->order_type === 'supplier';
    }

    public function getIsRetailerOrderAttribute()
    {
        return $this->order_type === 'retailer';
    }

    public function scopeSupplierOrders($query)
    {
        return $query->where('order_type', 'supplier');
    }

    public function scopeRetailerOrders($query)
    {
        return $query->where('order_type', 'retailer');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the retailer that owns the order.
     */
    public function retailer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'retailer_id');
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function factory()
    {
        return $this->belongsTo(\App\Models\Factory::class, 'factory_id');
    }

    public function products()
    {
        return $this->belongsToMany(\App\Models\Product::class, 'order_items', 'order_id', 'product_id');
    }

    public function wholesaler()
    {
        return $this->belongsTo(User::class, 'wholesaler_id');
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }
}
