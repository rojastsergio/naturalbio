<?php

namespace Modules\Appointments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class DoctorAvailability extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinic_id',
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'recurrence',
        'recurrence_end',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'start_time' => 'string',  // Cambiado de datetime a string para que funcione correctamente con los formatos de tiempo
        'end_time' => 'string',    // Cambiado de datetime a string para que funcione correctamente con los formatos de tiempo
        'recurrence_end' => 'date',
        'active' => 'boolean',
    ];

    /**
     * Scope a query to only include availabilities for a specific clinic.
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
     * Scope a query to only include availabilities for a specific doctor.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $doctorId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope a query to only include active availabilities.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include availabilities for a specific date or later.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFromDate($query, $date)
    {
        return $query->where('date', '>=', $date);
    }

    /**
     * Get the doctor that this availability belongs to.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}