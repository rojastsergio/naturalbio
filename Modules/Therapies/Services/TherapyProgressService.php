<?php

namespace Modules\Therapies\Services;

use Illuminate\Support\Facades\DB;
use Modules\Therapies\Models\TherapyAssignment;
use Modules\Therapies\Models\TherapyProgress;
use Exception;

class TherapyProgressService
{
    /**
     * Create a new therapy progress.
     *
     * @param array $data
     * @return TherapyProgress
     */
    public function createProgress(array $data)
    {
        try {
            DB::beginTransaction();
            
            $progress = TherapyProgress::create($data);
            
            DB::commit();
            return $progress;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing therapy progress.
     *
     * @param TherapyProgress $progress
     * @param array $data
     * @return TherapyProgress
     */
    public function updateProgress(TherapyProgress $progress, array $data)
    {
        try {
            DB::beginTransaction();
            
            $progress->update($data);
            
            DB::commit();
            return $progress;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update the status of a therapy assignment based on progress.
     *
     * @param TherapyAssignment $assignment
     * @param string $status
     * @return TherapyAssignment
     */
    public function updateAssignmentStatus(TherapyAssignment $assignment, $status)
    {
        try {
            DB::beginTransaction();
            
            $assignment->update(['status' => $status]);
            
            DB::commit();
            return $assignment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}