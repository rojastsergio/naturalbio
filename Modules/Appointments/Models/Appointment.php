<?php

namespace Modules\Appointments\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Patients\Models\Patient;
use App\Models\User;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinic_id',
        'patient_id',
        'doctor_id',
        'appointment_type_id',
        'start_time',
        'end_time',
        'status',
        'total_price',
        'notes',
        'doctor_notes',
        'emergency',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'emergency' => 'boolean',
        'total_price' => 'decimal:2',
    ];

    /**
     * Scope a query to only include appointments for a specific clinic.
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
     * Scope a query to only include appointments with a specific status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include future appointments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now());
    }

    /**
     * Get the patient that owns the appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor that owns the appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the type of this appointment.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(AppointmentType::class, 'appointment_type_id');
    }

    /**
     * Get the user who created the appointment.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the therapies for this appointment.
     */
    public function therapies(): BelongsToMany
    {
        return $this->belongsToMany('Modules\Therapies\Models\Therapy', 'appointment_therapy')
                    ->withPivot('price', 'duration', 'therapist_id')
                    ->withTimestamps();
    }
}