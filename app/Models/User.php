<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\Product;

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
        'status',
        'approved',
        'approval_message',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
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
        'approved' => 'boolean'
    ];

    /**
     * Available user roles
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUPPLIER = 'supplier';
    public const ROLE_FACTORY = 'factory';
    public const ROLE_WHOLESALER = 'wholesaler';
    public const ROLE_RETAILER = 'retailer';

    /**
     * Available user statuses
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_REJECTED = 'rejected';

    /**
     * Get available roles with their display names
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_SUPPLIER => 'Supplier',
            self::ROLE_FACTORY => 'Factory Manager',
            self::ROLE_WHOLESALER => 'Wholesaler',
            self::ROLE_RETAILER => 'Retailer'
        ];
    }

    /**
     * Get available statuses with their display names
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_REJECTED => 'Rejected'
        ];
    }

    /**
     * Create a new user with pending status
     */
    public static function createPending(array $data)
    {
        return self::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'contact_info' => $data['contact_info'] ?? null,
            'status' => self::STATUS_PENDING,
            'approved' => false,
            'approval_message' => 'Account pending approval'
        ]);
    }

    /**
     * Approve a user's account
     */
    public function approve(string $message = 'Account approved by admin')
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'approved' => true,
            'approval_message' => $message
        ]);
    }

    /**
     * Reject a user's account
     */
    public function reject(string $message = 'Account rejected by admin')
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved' => false,
            'approval_message' => $message
        ]);
    }

    /**
     * Suspend a user's account
     */
    public function suspend(string $message = 'Account suspended')
    {
        $this->update([
            'status' => self::STATUS_SUSPENDED,
            'approval_message' => $message
        ]);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return strtolower($this->role) === strtolower($role);
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user is approved
     */
    public function isApproved(): bool
    {
        return $this->approved === true;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if user is pending approval
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if user is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    /**
     * Check if user is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Get the dashboard route for the user based on their role
     */
    public function getDashboardRoute(): string
    {
        $role = strtolower($this->role);
        
        return match($role) {
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_FACTORY => 'factory.dashboard',
            self::ROLE_SUPPLIER => 'supplier.dashboard',
            self::ROLE_WHOLESALER => 'wholesaler.dashboard',
            self::ROLE_RETAILER => 'retailer.dashboard',
            default => 'home'
        };
    }

    /**
     * Scope a query to only include active users
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include pending users
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved users
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approved', true);
    }

    /**
     * Scope a query to only include rejected users
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to filter by role
     */
    public function scopeRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    /**
     * Get the orders for the retailer.
     */
    public function retailerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'retailer_id');
    }

    /**
     * Get the factory associated with the user.
     */
    public function factory()
    {
        return $this->hasOne(Factory::class);
    }

    /**
     * Get the products for the supplier user.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    /**
     * Get the orders for the wholesaler.
     */
    public function wholesalerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'wholesaler_id');
    }

    /**
     * Get the factories this wholesaler has ordered from (via orders).
     */
    public function orderedFactories()
    {
        return $this->hasManyThrough(
            \App\Models\Factory::class,
            \App\Models\Order::class,
            'wholesaler_id', // Foreign key on orders table...
            'id',            // Foreign key on factories table...
            'id',            // Local key on users table...
            'factory_id'     // Local key on orders table...
        )->distinct();
    }

    /**
     * The retailers that belong to the wholesaler.
     */
    public function retailers()
    {
        return $this->belongsToMany(User::class, 'retailer_wholesaler', 'wholesaler_id', 'retailer_id');
    }

    /**
     * The wholesalers that belong to the retailer.
     */
    public function wholesalers()
    {
        return $this->belongsToMany(User::class, 'retailer_wholesaler', 'retailer_id', 'wholesaler_id');
    }

    /**
     * Get the full URL for the user's profile image, or a default avatar if not set.
     */
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return asset('storage/profile_images/' . $this->profile_image);
        }
        // Use a default avatar (could be a static asset or a service like ui-avatars)
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }
}