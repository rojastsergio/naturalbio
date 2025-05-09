<?php

namespace Modules\Therapies\Services;

use Illuminate\Support\Facades\DB;
use Modules\Therapies\Models\Therapy;
use Exception;

class TherapyService
{
    /**
     * Get therapies with optional filtering.
     *
     * @param int $clinicId
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTherapies($clinicId, array $filters = [])
    {
        return Therapy::forClinic($clinicId)
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(isset($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Create a new therapy.
     *
     * @param array $data
     * @return Therapy
     */
    public function createTherapy(array $data)
    {
        try {
            DB::beginTransaction();
            
            $therapy = Therapy::create($data);
            
            DB::commit();
            return $therapy;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing therapy.
     *
     * @param Therapy $therapy
     * @param array $data
     * @return Therapy
     */
    public function updateTherapy(Therapy $therapy, array $data)
    {
        try {
            DB::beginTransaction();
            
            $therapy->update($data);
            
            DB::commit();
            return $therapy;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a therapy.
     *
     * @param Therapy $therapy
     * @return bool
     */
    public function deleteTherapy(Therapy $therapy)
    {
        try {
            DB::beginTransaction();
            
            // Check if therapy is in use
            if ($therapy->appointments()->count() > 0) {
                throw new Exception('No se puede eliminar esta terapia porque está siendo utilizada en citas.');
            }
            
            $therapy->delete();
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}