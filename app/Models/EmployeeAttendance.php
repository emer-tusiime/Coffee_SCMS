<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttendance extends Model
{
    protected $fillable = [
        'employee_id',
        'location_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Workforce::class, 'employee_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public static function getStatusOptions()
    {
        return [
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'leave' => 'On Leave'
        ];
    }

    public function getStatusLabelAttribute()
    {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? 'Unknown';
    }
}
