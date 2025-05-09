<?php

namespace Modules\Recommendations\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Recommendations\Models\PatientRecommendation;
use Modules\Patients\Models\Patient;
use Modules\Supplements\Models\Supplement;
use Modules\Therapies\Models\Therapy;

class RecommendationService
{
    /**
     * Obtener todas las recomendaciones con filtros.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllRecommendations(Request $request)
    {
        return PatientRecommendation::with(['patient', 'creator'])
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($request->has('patient_id'), function ($query) use ($request) {
                $query->where('patient_id', $request->input('patient_id'));
            })
            ->when($request->has('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->has('accepted'), function ($query) use ($request) {
                $query->where('accepted', $request->input('accepted'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Obtener recomendaciones de un paciente específico.
     *
     * @param Patient $patient
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPatientRecommendations(Patient $patient, Request $request)
    {
        return PatientRecommendation::with(['creator'])
            ->where('patient_id', $patient->id)
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Crear una nueva recomendación.
     *
     * @param array $data
     * @return PatientRecommendation
     */
    public function createRecommendation(array $data)
    {
        // Aseguramos que el creador sea el usuario actual
        $data['created_by'] = Auth::id();

        // Procesamos las tareas según el tipo de recomendación
        if (!isset($data['tasks']) || empty($data['tasks'])) {
            $data['tasks'] = $this->generateTasksByType(
                $data['type'], 
                $data['reference_id'] ?? null
            );
        }

        // Creamos la recomendación
        $recommendation = PatientRecommendation::create($data);

        return $recommendation;
    }

    /**
     * Generar tareas basadas en el tipo de recomendación.
     *
     * @param string $type
     * @param int|null $referenceId
     * @return array
     */
    protected function generateTasksByType(string $type, ?int $referenceId)
    {
        $tasks = [];

        if ($type === 'supplement' && $referenceId) {
            $supplement = Supplement::find($referenceId);
            if ($supplement) {
                $tasks[] = [
                    'title' => 'Tomar suplemento: ' . $supplement->name,
                    'description' => $supplement->instructions,
                    'completed' => false
                ];
            }
        } elseif ($type === 'therapy' && $referenceId) {
            $therapy = Therapy::find($referenceId);
            if ($therapy) {
                $tasks[] = [
                    'title' => 'Asistir a terapia: ' . $therapy->name,
                    'description' => $therapy->description,
                    'completed' => false
                ];
            }
        } elseif ($type === 'custom') {
            $tasks[] = [
                'title' => 'Tarea personalizada',
                'description' => '',
                'completed' => false
            ];
        }

        return $tasks;
    }

    /**
     * Actualizar una recomendación existente.
     *
     * @param PatientRecommendation $recommendation
     * @param array $data
     * @return PatientRecommendation
     */
    public function updateRecommendation(PatientRecommendation $recommendation, array $data)
    {
        $recommendation->update($data);

        // Si las tareas fueron actualizadas, recalculamos el progreso
        if (isset($data['tasks'])) {
            $recommendation->updateProgress();
        }

        return $recommendation;
    }

    /**
     * Aceptar o rechazar una recomendación.
     *
     * @param PatientRecommendation $recommendation
     * @param bool $accepted
     * @return PatientRecommendation
     */
    public function acceptOrReject(PatientRecommendation $recommendation, bool $accepted)
    {
        $recommendation->accepted = $accepted;
        $recommendation->save();

        return $recommendation;
    }

    /**
     * Completar una tarea específica.
     *
     * @param PatientRecommendation $recommendation
     * @param int $taskIndex
     * @param bool $completed
     * @return PatientRecommendation
     */
    public function completeTask(PatientRecommendation $recommendation, int $taskIndex, bool $completed)
    {
        $tasks = $recommendation->tasks;
        
        if (isset($tasks[$taskIndex])) {
            $tasks[$taskIndex]['completed'] = $completed;
            $recommendation->tasks = $tasks;
            $recommendation->save();
            
            // Actualizar el progreso
            $recommendation->updateProgress();
        }

        return $recommendation;
    }

    /**
     * Eliminar una recomendación.
     *
     * @param PatientRecommendation $recommendation
     * @return bool
     */
    public function deleteRecommendation(PatientRecommendation $recommendation)
    {
        return $recommendation->delete();
    }
}