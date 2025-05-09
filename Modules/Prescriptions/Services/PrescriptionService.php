<?php

namespace Modules\Prescriptions\Services;

use Modules\Prescriptions\Models\Prescription;
use Modules\Prescriptions\Models\PrescriptionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Exception;

class PrescriptionService
{
    /**
     * Create a new prescription with its items.
     *
     * @param array $prescriptionData
     * @param array $itemsData
     * @return Prescription
     */
    public function createPrescription(array $prescriptionData, array $itemsData): Prescription
    {
        DB::beginTransaction();

        try {
            // Create prescription
            $prescription = Prescription::create($prescriptionData);

            // Create prescription items
            foreach ($itemsData as $itemData) {
                $itemData['prescription_id'] = $prescription->id;
                PrescriptionItem::create($itemData);
            }

            DB::commit();
            return $prescription;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing prescription with its items.
     *
     * @param Prescription $prescription
     * @param array $prescriptionData
     * @param array $itemsData
     * @return Prescription
     */
    public function updatePrescription(Prescription $prescription, array $prescriptionData, array $itemsData): Prescription
    {
        DB::beginTransaction();

        try {
            // Update prescription
            $prescription->update($prescriptionData);

            // Delete existing items
            $prescription->items()->delete();

            // Create new items
            foreach ($itemsData as $itemData) {
                $itemData['prescription_id'] = $prescription->id;
                PrescriptionItem::create($itemData);
            }

            DB::commit();
            return $prescription;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate PDF for a prescription.
     *
     * @param Prescription $prescription
     * @return string
     */
    public function generatePdf(Prescription $prescription): string
    {
        // PDF generation logic would go here
        // This is a placeholder
        
        return "pdf_path_for_prescription_{$prescription->id}.pdf";
    }

    /**
     * Send notification to patient about the prescription.
     *
     * @param Prescription $prescription
     * @return bool
     */
    public function sendNotification(Prescription $prescription): bool
    {
        // Notification logic would go here (email, SMS, WhatsApp)
        // This is a placeholder
        
        $prescription->update(['notification_sent' => true]);
        return true;
    }
}