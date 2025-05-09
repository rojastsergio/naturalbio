<?php

namespace Modules\Appointments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppointmentType extends Model
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
        'color',
        'description',
        'default_price',
        'default_duration',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'default_price' => 'decimal:2',
        'default_duration' => 'integer',
        'active' => 'boolean',
    ];

    /**
     * Scope a query to only include appointment types for a specific clinic.
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
     * Scope a query to only include active appointment types.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get the appointments for this type.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'appointment_type_id');
    }
}