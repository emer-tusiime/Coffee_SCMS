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

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'contact_info',
        'status',
        'approved',
        'approval_message'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'approved' => 'boolean'
    ];

    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUPPLIER = 'supplier';
    public const ROLE_FACTORY = 'factory';
    public const ROLE_WHOLESALER = 'wholesaler';
    public const ROLE_RETAILER = 'retailer';

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_REJECTED = 'rejected';

    public static function getRoles(): array
    {
        return [
            self::ROLE_SUPPLIER => 'Supplier',
            self::ROLE_FACTORY => 'Factory Manager',
            self::ROLE_WHOLESALER => 'Wholesaler',
            self::ROLE_RETAILER => 'Retailer'
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_REJECTED => 'Rejected'
        ];
    }

    public static function createPending(array $data)
    {
        return self::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'contact_info' => $data['contact_info'],
            'status' => self::STATUS_PENDING,
            'approved' => false,
            'approval_message' => 'Account pending approval'
        ]);
    }

    public function approve(string $message = 'Account approved by admin')
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'approved' => true,
            'approval_message' => $message
        ]);
    }

    public function reject(string $message = 'Account rejected by admin')
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved' => false,
            'approval_message' => $message
        ]);
    }

    public function suspend(string $message = 'Account suspended')
    {
        $this->update([
            'status' => self::STATUS_SUSPENDED,
            'approval_message' => $message
        ]);
    }

    public function hasRole(string $role): bool
    {
        return strtolower($this->role) === strtolower($role);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isApproved(): bool
    {
        return $this->approved === true;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approved', true);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    public function retailerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'retailer_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
