<?php

namespace Modules\Doctors\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Patients\Models\Patient;
use Modules\Appointments\Models\Appointment;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'clinic_id',
        'specialty',
        'license_number',
        'biography',
        'consultation_fee',
        'accepts_emergencies'
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'consultation_fee' => 'decimal:2',
        'accepts_emergencies' => 'boolean',
    ];

    /**
     * Obtener el usuario asociado al doctor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener los pacientes del doctor.
     */
    public function patients()
    {
        return $this->hasManyThrough(
            Patient::class,
            Appointment::class,
            'doctor_id',
            'id',
            'id',
            'patient_id'
        )->distinct();
    }

    /**
     * Obtener las citas del doctor.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Obtener las disponibilidades del doctor.
     */
    public function availabilities()
    {
        return $this->hasMany(DoctorAvailability::class);
    }

    /**
     * Obtener las instrucciones terapéuticas del doctor.
     */
    public function therapyInstructions()
    {
        return $this->hasMany(TherapyInstruction::class);
    }

    /**
     * Scope para filtrar por clínica.
     */
    public function scopeByClinic($query, $clinicId)
    {
        return $query->where('clinic_id', $clinicId);
    }

    /**
     * Scope para filtrar por especialidad.
     */
    public function scopeBySpecialty($query, $specialty)
    {
        return $query->where('specialty', $specialty);
    }

    /**
     * Scope para filtrar doctores que aceptan emergencias.
     */
    public function scopeAcceptsEmergencies($query)
    {
        return $query->where('accepts_emergencies', true);
    }
}