<?php

namespace Modules\Therapies\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TherapyAssignment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'appointment_id',
        'therapy_id',
        'therapist_id',
        'price',
        'duration',
        'status',
        'notes',
        'assigned_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
    ];

    /**
     * Get the appointment that owns the assignment.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(\Modules\Appointments\Models\Appointment::class);
    }

    /**
     * Get the therapy that owns the assignment.
     */
    public function therapy(): BelongsTo
    {
        return $this->belongsTo(Therapy::class);
    }

    /**
     * Get the therapist assigned to this therapy.
     */
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'therapist_id');
    }

    /**
     * Get the user who assigned this therapy.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_by');
    }

    /**
     * Get the progress records for this assignment.
     */
    public function progresses(): HasMany
    {
        return $this->hasMany(TherapyProgress::class);
    }

    /**
     * Get the latest progress record.
     */
    public function latestProgress()
    {
        return $this->hasOne(TherapyProgress::class)->latest();
    }

    /**
     * Calculate the overall progress percentage.
     */
    public function getProgressPercentageAttribute()
    {
        $progresses = $this->progresses;
        
        if ($progresses->isEmpty()) {
            return 0;
        }
        
        return round($progresses->avg('progress_percentage'));
    }
}