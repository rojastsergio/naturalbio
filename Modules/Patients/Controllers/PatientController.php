<?php

namespace Modules\Patients\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Patients\Models\Patient;
use Modules\Patients\Services\PatientService;
use App\Models\Municipality;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PatientController extends Controller
{
    protected $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    /**
     * Display a listing of the patients.
     */
    public function index(Request $request)
    {
        $patients = Patient::with(['municipality.department', 'latestVitalSigns'])
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('expedient_number', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->has('municipality_id'), function ($query) use ($request) {
                $query->where('municipality_id', $request->input('municipality_id'));
            })
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Patients/Index', [
            'patients' => $patients,
            'filters' => $request->only(['search', 'municipality_id', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create()
    {
        // Cargar municipios con su departamento asociado
        $municipalities = Municipality::with('department')
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values()
            ->map(function ($municipality) {
                return [
                    'id' => $municipality->id,
                    'name' => $municipality->name,
                    'code' => $municipality->code,
                    'department' => [
                        'id' => $municipality->department->id,
                        'name' => $municipality->department->name,
                        'code' => $municipality->department->code,
                    ]
                ];
            })
            ->all();

        return Inertia::render('Patients/Create', [
            'municipalities' => $municipalities,
        ]);
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'municipality_id' => 'nullable|exists:municipalities,id',
            'medical_history' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|string',
            'vital_signs' => 'nullable|array',
            'vital_signs.blood_pressure' => 'nullable|string',
            'vital_signs.oxygen_saturation' => 'nullable|numeric|min:0|max:100',
            'vital_signs.blood_glucose' => 'nullable|numeric|min:0',
            'vital_signs.heart_rate' => 'nullable|integer|min:0',
            'vital_signs.respiratory_rate' => 'nullable|integer|min:0',
            'vital_signs.height' => 'nullable|numeric|min:0',
            'vital_signs.weight' => 'nullable|numeric|min:0',
            'vital_signs.notes' => 'nullable|string',
        ]);

        $patientData = $request->except(['photo', 'signature', 'vital_signs']);
        $patientData['clinic_id'] = session('clinic_id', 1); // Default to clinic_id 1 if not set

        $vitalSignsData = $request->input('vital_signs');
        if ($vitalSignsData) {
            $vitalSignsData['recorded_by'] = Auth::id();
            $vitalSignsData['recorded_at'] = now();
        }

        $photo = $request->file('photo');
        $signature = $request->input('signature');

        $patient = $this->patientService->createPatient(
            $patientData,
            $vitalSignsData,
            $photo,
            $signature
        );

        // Si se incluye el parámetro redirect_after, redireccionar a esa ruta
        if ($request->has('redirect_after')) {
            return redirect($request->input('redirect_after'))
                ->with('success', 'Paciente creado exitosamente.');
        }

        // En caso contrario, redireccionar a la vista de detalle del paciente
        return redirect()->route('patients.show', $patient)
            ->with('success', 'Paciente creado exitosamente.');
    }

    /**
     * Display the specified patient.
     */
    public function show(Patient $patient)
    {
        $patient->load(['municipality.department', 'vitalSigns' => function ($query) {
            $query->orderBy('recorded_at', 'desc');
        }, 'vitalSigns.recordedBy']);

        return Inertia::render('Patients/Show', [
            'patient' => $patient,
        ]);
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Patient $patient)
    {
        $patient->load('municipality.department');
        
        // Cargar municipios con su departamento asociado
        $municipalities = Municipality::with('department')
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values()
            ->map(function ($municipality) {
                return [
                    'id' => $municipality->id,
                    'name' => $municipality->name,
                    'code' => $municipality->code,
                    'department' => [
                        'id' => $municipality->department->id,
                        'name' => $municipality->department->name,
                        'code' => $municipality->department->code,
                    ]
                ];
            })
            ->all();

        return Inertia::render('Patients/Edit', [
            'patient' => $patient,
            'municipalities' => $municipalities,
        ]);
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'municipality_id' => 'nullable|exists:municipalities,id',
            'medical_history' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
            'photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|string',
        ]);

        $patientData = $request->except(['photo', 'signature']);
        $photo = $request->file('photo');
        $signature = $request->input('signature');

        $this->patientService->updatePatient(
            $patient,
            $patientData,
            $photo,
            $signature
        );

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Paciente actualizado exitosamente.');
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Paciente eliminado exitosamente.');
    }

    /**
     * Search for patients by name, last name or expedient number
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $search = $request->input('search', '');

        if (strlen($search) < 2) {
            return response()->json(['patients' => []]);
        }

        // Query simple para evitar problemas
        $patients = [];

        // Obtener pacientes de la base de datos
        $query = Patient::query();

        // Filtrar por nombre, apellido o número de expediente
        $query->where('name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('expedient_number', 'like', "%{$search}%");

        // Limitar resultados
        $query->limit(10);

        // Ejecutar consulta
        $results = $query->get();

        // Formatear resultados
        foreach ($results as $patient) {
            $patients[] = [
                'id' => $patient->id,
                'name' => $patient->name,
                'last_name' => $patient->last_name,
                'expedient_number' => $patient->expedient_number,
                'full_name' => "{$patient->name} {$patient->last_name}" . ($patient->expedient_number ? " (#{$patient->expedient_number})" : "")
            ];
        }

        return response()->json([
            'patients' => $patients
        ]);
    }
    
    /**
     * Search for municipalities
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchMunicipalities(Request $request)
    {
        $search = $request->input('q', '');

        if (strlen($search) < 2) {
            return response()->json(['municipios' => []]);
        }

        $municipios = \App\Models\Municipality::with('department')
            ->where('name', 'like', "%{$search}%")
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(function($municipio) {
                return [
                    'id' => $municipio->id,
                    'nombre' => $municipio->name,
                    'code' => $municipio->code,
                    'departamento' => [
                        'id' => $municipio->department->id,
                        'nombre' => $municipio->department->name,
                        'code' => $municipio->department->code,
                    ]
                ];
            });

        return response()->json(['municipios' => $municipios]);
    }

    /**
     * Get municipality by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMunicipality($id)
    {
        $municipio = \App\Models\Municipality::with('department')->find($id);

        if (!$municipio) {
            return response()->json(['error' => 'Municipio no encontrado'], 404);
        }

        return response()->json([
            'municipio' => [
                'id' => $municipio->id,
                'nombre' => $municipio->name,
                'code' => $municipio->code,
                'departamento' => [
                    'id' => $municipio->department->id,
                    'nombre' => $municipio->department->name,
                    'code' => $municipio->department->code,
                ]
            ]
        ]);
    }
}