<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'contact_info',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the orders for the customer.
     */
    public function customerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get the orders for the retailer.
     */
    public function retailerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'retailer_id');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role): bool
    {
        return strtolower($this->role) === strtolower($role);
    }

    /**
     * Get available roles with their display names
     */
    public static function getRoles(): array
    {
        return [
            'admin' => 'Administrator',
            'factory' => 'Factory Manager',
            'supplier' => 'Supplier',
            'customer' => 'Customer',
            'retailer' => 'Retailer',
            'wholesaler' => 'Wholesaler',
            'workforce_manager' => 'Workforce Manager'
        ];
    }

    /**
     * Get the dashboard route for the user based on their role
     */
    public function getDashboardRoute(): string
    {
        return match(strtolower($this->role)) {
            'admin' => 'admin.dashboard',
            'factory' => 'factory.dashboard',
            'supplier' => 'supplier.dashboard',
            'customer' => 'customer.dashboard',
            'retailer' => 'retailer.dashboard',
            'wholesaler' => 'wholesaler.dashboard',
            'workforce_manager' => 'workforce_manager.dashboard',
            default => 'login'
        };
    }

    /**
     * Check if the user is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Scope a query to only include active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
