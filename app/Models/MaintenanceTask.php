<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_line_id',
        'title',
        'description',
        'priority',
        'status',
        'scheduled_date',
        'completed_date',
        'assigned_to',
        'notes',
        'maintenance_type'
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'completed_date' => 'datetime'
    ];

    // Relationship with ProductionLine
    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class);
    }

    // Relationship with User (assigned technician)
    public function assignedTechnician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scope for pending tasks
    public function scopePending($query)
    {
        return $query->whereIn('status', ['scheduled', 'in_progress']);
    }

    // Scope for overdue tasks
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'completed')
                    ->where('scheduled_date', '<', now());
    }

    // Scope for completed tasks
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
