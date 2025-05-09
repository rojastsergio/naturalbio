<?php

namespace Modules\Therapies\Services;

use Illuminate\Support\Facades\DB;
use Modules\Therapies\Models\TherapyAssignment;
use Exception;

class TherapyAssignmentService
{
    /**
     * Get assignments for a therapist with optional filtering.
     *
     * @param int $therapistId
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAssignments($therapistId, array $filters = [])
    {
        return TherapyAssignment::with(['therapy', 'appointment.patient', 'latestProgress'])
            ->when($therapistId, function ($query) use ($therapistId) {
                $query->where('therapist_id', $therapistId);
            })
            ->when(isset($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(isset($filters['date']), function ($query) use ($filters) {
                $query->whereHas('appointment', function ($q) use ($filters) {
                    $q->whereDate('start_time', $filters['date']);
                });
            })
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->whereHas('therapy', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('appointment.patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Update an existing assignment.
     *
     * @param TherapyAssignment $assignment
     * @param array $data
     * @return TherapyAssignment
     */
    public function updateAssignment(TherapyAssignment $assignment, array $data)
    {
        try {
            DB::beginTransaction();
            
            $assignment->update($data);
            
            DB::commit();
            return $assignment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}