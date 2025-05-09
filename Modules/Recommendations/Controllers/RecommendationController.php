<?php

namespace Modules\Recommendations\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Recommendations\Models\PatientRecommendation;
use Modules\Recommendations\Services\RecommendationService;
use Modules\Patients\Models\Patient;
use Modules\Supplements\Models\Supplement;
use Modules\Therapies\Models\Therapy;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
        
        // Aplicar middleware de permisos con el formato correcto
        $this->middleware('permission:view recommendations')->only(['index', 'show']);
        $this->middleware('permission:create recommendations')->only(['create', 'store']);
        $this->middleware('permission:update recommendations')->only(['edit', 'update', 'acceptOrReject', 'completeTask']);
        $this->middleware('permission:delete recommendations')->only('destroy');
    }

    /**
     * Mostrar una lista de recomendaciones.
     */
    public function index(Request $request)
    {
        $recommendations = $this->recommendationService->getAllRecommendations($request);

        return Inertia::render('Recommendations/Index', [
            'recommendations' => $recommendations,
            'filters' => $request->only(['search', 'patient_id', 'type', 'status', 'accepted']),
        ]);
    }

    /**
     * Mostrar el formulario para crear una nueva recomendación.
     */
    public function create()
    {
        $patients = Patient::where('status', 'active')->get(['id', 'name', 'last_name']);
        $supplements = Supplement::where('status', 'active')->get(['id', 'name']);
        $therapies = Therapy::where('status', 'active')->get(['id', 'name']);

        return Inertia::render('Recommendations/Create', [
            'patients' => $patients,
            'supplements' => $supplements,
            'therapies' => $therapies,
        ]);
    }

    /**
     * Almacenar una nueva recomendación en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:supplement,therapy,questionnaire,custom',
            'reference_id' => 'nullable|integer',
            'tasks' => 'nullable|array',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.completed' => 'boolean',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $recommendation = $this->recommendationService->createRecommendation($validated);

        return redirect()->route('recommendations.show', $recommendation)
            ->with('success', 'Recomendación creada exitosamente.');
    }

    /**
     * Mostrar la información de una recomendación específica.
     */
    public function show(PatientRecommendation $recommendation)
{
    // Cargar relaciones básicas
    $recommendation->load(['patient', 'creator']);

    // Cargar relaciones específicas basadas en el tipo
    if ($recommendation->type === 'supplement') {
        $recommendation->load('supplement');
    } elseif ($recommendation->type === 'therapy') {
        $recommendation->load('therapy');
    }

    return Inertia::render('Recommendations/Show', [
        'recommendation' => $recommendation,
    ]);
}

    /**
     * Mostrar el formulario para editar una recomendación.
     */
    public function edit(PatientRecommendation $recommendation)
    {
        $recommendation->load('patient');

        $patients = Patient::where('status', 'active')->get(['id', 'name', 'last_name']);
        $supplements = Supplement::where('status', 'active')->get(['id', 'name']);
        $therapies = Therapy::where('status', 'active')->get(['id', 'name']);

        return Inertia::render('Recommendations/Edit', [
            'recommendation' => $recommendation,
            'patients' => $patients,
            'supplements' => $supplements,
            'therapies' => $therapies,
        ]);
    }

    /**
     * Actualizar la información de una recomendación en la base de datos.
     */
    public function update(Request $request, PatientRecommendation $recommendation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tasks' => 'nullable|array',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.completed' => 'boolean',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:active,completed,cancelled',
        ]);

        $this->recommendationService->updateRecommendation($recommendation, $validated);

        return redirect()->route('recommendations.show', $recommendation)
            ->with('success', 'Recomendación actualizada exitosamente.');
    }

    /**
     * Aceptar o rechazar una recomendación (para pacientes).
     */
    public function acceptOrReject(Request $request, PatientRecommendation $recommendation)
    {
        $request->validate([
            'accepted' => 'required|boolean',
        ]);

        $this->recommendationService->acceptOrReject(
            $recommendation, 
            $request->input('accepted')
        );

        return back()->with('success', 'Estado de aceptación actualizado correctamente.');
    }

    /**
     * Marcar una tarea como completada/pendiente.
     */
    public function completeTask(Request $request, PatientRecommendation $recommendation)
    {
        $request->validate([
            'task_index' => 'required|integer|min:0',
            'completed' => 'required|boolean',
        ]);

        $this->recommendationService->completeTask(
            $recommendation,
            $request->input('task_index'),
            $request->input('completed')
        );

        return back()->with('success', 'Tarea actualizada correctamente.');
    }

    /**
     * Eliminar una recomendación de la base de datos.
     */
    public function destroy(PatientRecommendation $recommendation)
    {
        $this->recommendationService->deleteRecommendation($recommendation);

        return redirect()->route('recommendations.index')
            ->with('success', 'Recomendación eliminada exitosamente.');
    }

    /**
     * Mostrar recomendaciones para un paciente específico.
     */
    public function forPatient(Request $request, Patient $patient)
    {
        $recommendations = $this->recommendationService->getPatientRecommendations($patient, $request);

        return Inertia::render('Recommendations/PatientRecommendations', [
            'patient' => $patient,
            'recommendations' => $recommendations,
            'filters' => $request->only(['status']),
        ]);
    }
}