<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'clinic_id',
        'expedient_number',
        'name',
        'last_name',
        'email',
        'phone',
        'whatsapp',
        'birthdate',
        'gender',
        'address',
        'municipality_id',
        'medical_history',
        'photo_path',
        'signature_path',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthdate' => 'date',
    ];

    /**
     * Get the clinic that owns the patient.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Clinic::class);
    }

    /**
     * Get the municipality of the patient.
     */
    public function municipality(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Municipality::class);
    }

    /**
     * Get the vital signs for the patient.
     */
    public function vitalSigns(): HasMany
    {
        return $this->hasMany(VitalSign::class);
    }

    /**
     * Get the latest vital signs record.
     */
    public function latestVitalSigns()
    {
        return $this->hasOne(VitalSign::class)->latest();
    }

    /**
     * Get the appointments for the patient.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(\Modules\Appointments\Models\Appointment::class);
    }

    /**
     * Get the full name of the patient.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->last_name}";
    }

    /**
     * Get the age of the patient.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birthdate ? $this->birthdate->age : null;
    }
}