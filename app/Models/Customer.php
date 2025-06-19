<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_info',
        'segment_id',
    ];

    // Relationships
    public function segment()
    {
        return $this->belongsTo(CustomerSegment::class, 'segment_id');
    }
}