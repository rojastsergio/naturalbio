<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VitalSign extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'recorded_at',
        'blood_pressure',
        'oxygen_saturation',
        'blood_glucose',
        'heart_rate',
        'respiratory_rate',
        'height',
        'weight',
        'bmi',
        'notes',
        'recorded_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'recorded_at' => 'date',
        'oxygen_saturation' => 'float',
        'blood_glucose' => 'float',
        'heart_rate' => 'integer',
        'respiratory_rate' => 'integer',
        'height' => 'float',
        'weight' => 'float',
        'bmi' => 'float',
    ];

    /**
     * Get the patient that owns the vital signs.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the user who recorded these vital signs.
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }

    /**
     * Calculate BMI when setting height and weight.
     */
    protected static function booted()
    {
        static::creating(function ($vitalSign) {
            if ($vitalSign->height && $vitalSign->weight) {
                // Height in meters (converting from cm)
                $heightInMeters = $vitalSign->height / 100;
                
                // Formula: weight (kg) / (height (m) * height (m))
                $vitalSign->bmi = $vitalSign->weight / ($heightInMeters * $heightInMeters);
            }
        });

        static::updating(function ($vitalSign) {
            if ($vitalSign->isDirty(['height', 'weight']) && $vitalSign->height && $vitalSign->weight) {
                $heightInMeters = $vitalSign->height / 100;
                $vitalSign->bmi = $vitalSign->weight / ($heightInMeters * $heightInMeters);
            }
        });
    }
}