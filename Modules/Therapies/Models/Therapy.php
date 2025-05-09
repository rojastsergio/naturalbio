<?php

namespace Modules\Therapies\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Therapy extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinic_id',
        'name',
        'description',
        'default_price',
        'price',
        'default_duration',
        'media',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'default_price' => 'decimal:2',
        'price' => 'decimal:2',
        'default_duration' => 'integer',
        'media' => 'array',
    ];

    /**
     * Scope a query to only include active therapies.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include therapies for a specific clinic.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $clinicId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForClinic($query, $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    /**
     * Get the creator of the therapy.
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get the appointments for this therapy.
     */
    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(\Modules\Appointments\Models\Appointment::class, 'appointment_therapy')
                    ->withPivot('price', 'duration', 'therapist_id')
                    ->withTimestamps();
    }

    /**
     * Get the assignments for this therapy.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(TherapyAssignment::class);
    }

    /**
     * Get the instructions for this therapy.
     */
    public function instructions(): HasMany
    {
        return $this->hasMany(TherapyInstruction::class);
    }
}