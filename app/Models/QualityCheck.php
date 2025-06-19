<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_line_id',
        'batch_number',
        'temperature',
        'humidity',
        'roast_level',
        'aroma_score',
        'flavor_score',
        'acidity_score',
        'body_score',
        'aftertaste_score',
        'overall_score',
        'moisture_content',
        'defect_count',
        'checked_by',
        'status',
        'notes'
    ];

    protected $casts = [
        'temperature' => 'float',
        'humidity' => 'float',
        'moisture_content' => 'float',
        'aroma_score' => 'float',
        'flavor_score' => 'float',
        'acidity_score' => 'float',
        'body_score' => 'float',
        'aftertaste_score' => 'float',
        'overall_score' => 'float',
        'defect_count' => 'integer'
    ];

    // Relationship with ProductionLine
    public function productionLine()
    {
        return $this->belongsTo(ProductionLine::class);
    }

    // Relationship with User (quality checker)
    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    // Calculate total quality score
    public function calculateTotalScore()
    {
        return ($this->aroma_score +
                $this->flavor_score +
                $this->acidity_score +
                $this->body_score +
                $this->aftertaste_score +
                $this->overall_score) / 6;
    }

    // Scope for failed checks
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Scope for passed checks
    public function scopePassed($query)
    {
        return $query->where('status', 'passed');
    }

    // Scope for pending checks
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Check if quality check passed minimum standards
    public function passedMinimumStandards()
    {
        $totalScore = $this->calculateTotalScore();
        $minimumScore = 7.0; // Minimum acceptable score
        $maximumDefects = 5; // Maximum acceptable defects

        return $totalScore >= $minimumScore && $this->defect_count <= $maximumDefects;
    }
}
