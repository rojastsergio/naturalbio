<?php

namespace Modules\Doctors\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorAvailability extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'doctor_id',
        'clinic_id',
        'date',
        'start_time',
        'end_time',
        'recurrence',
        'recurrence_end',
        'active'
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'recurrence_end' => 'date',
        'active' => 'boolean',
    ];

    /**
     * Obtener el doctor asociado a esta disponibilidad.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Scope para filtrar por doctor.
     */
    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    /**
     * Scope para filtrar por fecha.
     */
    public function scopeByDate($query, $date)
    {
        return $query->where('date', $date);
    }

    /**
     * Scope para filtrar por rango de fechas.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope para filtrar disponibilidades activas.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}