<?php

namespace Modules\Prescriptions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
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
        'prescription_number',
        'issue_date',
        'expiry_date',
        'diagnosis',
        'instructions',
        'status',
        'notification_sent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'notification_sent' => 'boolean',
    ];

    /**
     * Get the clinic that owns the prescription.
     */
    public function clinic(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Clinic::class);
    }

    /**
     * Get the patient that owns the prescription.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(\Modules\Patients\Models\Patient::class);
    }

    /**
     * Get the doctor that created the prescription.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(\Modules\Doctors\Models\Doctor::class);
    }

    /**
     * Get the items for this prescription.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    /**
     * Generate a PDF for this prescription.
     */
    public function generatePdf()
    {
        // Logic for PDF generation will be implemented here
        // or in a dedicated service
    }

    /**
     * Send notification to patient about this prescription.
     */
    public function sendNotification()
    {
        // Logic for sending notifications will be implemented here
        // or in a dedicated service
    }
}