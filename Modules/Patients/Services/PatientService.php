<?php

namespace Modules\Patients\Services;

use Illuminate\Support\Facades\Storage;
use Modules\Patients\Models\Patient;
use Modules\Patients\Models\VitalSign;

class PatientService
{
    /**
     * Create a new patient with all related data.
     *
     * @param array $patientData Patient basic data
     * @param array|null $vitalSignsData Initial vital signs
     * @param \Illuminate\Http\UploadedFile|null $photo Patient photo
     * @param string|null $signature Base64 encoded signature
     * @return Patient
     */
    public function createPatient(array $patientData, ?array $vitalSignsData = null, $photo = null, $signature = null): Patient
    {
        // Process photo if provided
        if ($photo) {
            $patientData['photo_path'] = $this->storePhoto($photo);
        }

        // Process signature if provided
        if ($signature) {
            $patientData['signature_path'] = $this->storeSignature($signature);
        }

        // Generate expedient number if not provided
        if (empty($patientData['expedient_number'])) {
            $patientData['expedient_number'] = $this->generateExpedientNumber($patientData['clinic_id']);
        }

        // Create the patient
        $patient = Patient::create($patientData);

        // Create vital signs if provided
        if ($vitalSignsData) {
            $vitalSignsData['patient_id'] = $patient->id;
            $vitalSignsData['recorded_at'] = $vitalSignsData['recorded_at'] ?? now();
            VitalSign::create($vitalSignsData);
        }

        return $patient;
    }

    /**
     * Update an existing patient.
     *
     * @param Patient $patient
     * @param array $patientData
     * @param \Illuminate\Http\UploadedFile|null $photo
     * @param string|null $signature
     * @return Patient
     */
    public function updatePatient(Patient $patient, array $patientData, $photo = null, $signature = null): Patient
    {
        // Process photo if provided
        if ($photo) {
            // Delete old photo if exists
            if ($patient->photo_path) {
                Storage::delete($patient->photo_path);
            }
            $patientData['photo_path'] = $this->storePhoto($photo);
        }

        // Process signature if provided
        if ($signature) {
            // Delete old signature if exists
            if ($patient->signature_path) {
                Storage::delete($patient->signature_path);
            }
            $patientData['signature_path'] = $this->storeSignature($signature);
        }

        $patient->update($patientData);

        return $patient;
    }

    /**
     * Store a patient photo.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return string
     */
    private function storePhoto($photo): string
    {
        return $photo->store('patients/photos', 'public');
    }

    /**
     * Store a patient signature from base64.
     *
     * @param string $signatureBase64
     * @return string
     */
    private function storeSignature(string $signatureBase64): string
    {
        // Extract the base64 data
        $signatureData = substr($signatureBase64, strpos($signatureBase64, ',') + 1);
        $decodedSignature = base64_decode($signatureData);

        // Generate a unique filename
        $filename = 'patients/signatures/' . uniqid() . '.png';
        
        // Store the file
        Storage::disk('public')->put($filename, $decodedSignature);

        return $filename;
    }

    /**
     * Generate a unique expedient number based on clinic settings.
     *
     * @param int $clinicId
     * @return string
     */
    private function generateExpedientNumber(int $clinicId): string
    {
        // This should be refactored to use the clinic settings
        $prefix = 'PAC-' . $clinicId . '-';
        $lastPatient = Patient::where('clinic_id', $clinicId)
            ->where('expedient_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $sequentialNumber = 1;
        if ($lastPatient && $lastPatient->expedient_number) {
            // Extract the sequential number from the expedient number
            $parts = explode('-', $lastPatient->expedient_number);
            $lastSequentialNumber = end($parts);
            $sequentialNumber = (int) $lastSequentialNumber + 1;
        }

        // Format with leading zeros (e.g., PAC-1-00001)
        return $prefix . str_pad($sequentialNumber, 5, '0', STR_PAD_LEFT);
    }
}