<?php

namespace Modules\Doctors\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\Doctors\Models\Doctor;
use Modules\Doctors\Services\DoctorService;

class DoctorController extends Controller
{
    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    /**
     * Mostrar una lista de doctores.
     */
    public function index(Request $request)
    {
        $doctors = $this->doctorService->getAllDoctors($request);

        return Inertia::render('Doctors/Index', [
            'doctors' => $doctors,
            'filters' => $request->only(['search', 'specialty', 'status']),
        ]);
    }

    /**
     * Mostrar el formulario para crear un nuevo doctor.
     */
    public function create()
    {
        return Inertia::render('Doctors/Create');
    }

    /**
     * Almacenar un nuevo doctor en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialty' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
            'accepts_emergencies' => 'boolean',
        ]);

        $doctor = $this->doctorService->createDoctor($validated);

        return redirect()->route('doctors.show', $doctor->id)
            ->with('success', 'Doctor creado exitosamente.');
    }

    /**
     * Mostrar el perfil de un doctor específico.
     */
    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'appointments' => function ($query) {
            $query->where('start_time', '>=', now())
                ->orderBy('start_time', 'asc')
                ->take(5);
        }]);

        // Inicializar estadísticas con valores predeterminados
        $statistics = [
            'totalPatients' => 0,
            'completedAppointments' => 0,
            'assignedTherapies' => 0,
            'totalRevenue' => 0,
        ];

        // Calcular estadísticas reales si es posible
        if ($doctor->exists) {
            try {
                // Obtenemos el número de pacientes únicos
                $totalPatients = $doctor->appointments()
                    ->select('patient_id')
                    ->distinct()
                    ->count('patient_id');

                // Contamos las citas completadas
                $completedAppointments = $doctor->appointments()
                    ->where('status', 'completed')
                    ->count();

                // Calculamos los ingresos totales
                $totalRevenue = $doctor->appointments()
                    ->where('status', 'completed')
                    ->sum('price');

                // Contamos las terapias asignadas si existe la relación
                $assignedTherapies = 0;
                if (method_exists($doctor, 'therapyAssignments')) {
                    $assignedTherapies = $doctor->therapyAssignments()->count();
                }

                $statistics = [
                    'totalPatients' => $totalPatients,
                    'completedAppointments' => $completedAppointments,
                    'assignedTherapies' => $assignedTherapies,
                    'totalRevenue' => $totalRevenue ?? 0,
                ];
            } catch (\Exception $e) {
                // Log del error pero mantenemos las estadísticas por defecto
                Log::error("Error calculando estadísticas para doctor #{$doctor->id}: " . $e->getMessage());
            }
        }

        return Inertia::render('Doctors/Show', [
            'doctor' => $doctor,
            'statistics' => $statistics // Añadimos las estadísticas a la respuesta
        ]);
    }

    /**
     * Mostrar el formulario para editar un doctor.
     */
    public function edit(Doctor $doctor)
    {
        $doctor->load('user');

        return Inertia::render('Doctors/Edit', [
            'doctor' => $doctor,
        ]);
    }

    /**
     * Actualizar la información de un doctor en la base de datos.
     */
    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'specialty' => 'nullable|string|max:255',
            'license_number' => 'nullable|string|max:255',
            'biography' => 'nullable|string',
            'consultation_fee' => 'nullable|numeric|min:0',
            'accepts_emergencies' => 'boolean',
        ]);

        $this->doctorService->updateDoctor($doctor, $validated);

        return redirect()->route('doctors.show', $doctor->id)
            ->with('success', 'Doctor actualizado exitosamente.');
    }

    /**
     * Eliminar un doctor de la base de datos.
     */
    public function destroy(Doctor $doctor)
    {
        $this->doctorService->deleteDoctor($doctor);

        return redirect()->route('doctors.index')
            ->with('success', 'Doctor eliminado exitosamente.');
    }

    /**
     * Mostrar el panel de control del doctor.
     */
    public function dashboard(Request $request)
    {
        // Verificar si el usuario tiene el rol de doctor
        if (!Auth::user()->hasRole('doctor')) {
            return redirect()->route('doctors.index')
                ->with('error', 'Acceso denegado: No tienes el rol de doctor asignado. Contacta al administrador para solicitar el acceso.');
        }
        
        $doctor = $this->doctorService->getCurrentDoctor();
        
        if (!$doctor) {
            // Si el usuario tiene el rol pero no tiene perfil, algo salió mal
            return redirect()->route('doctors.index')
                ->with('error', 'Error en la configuración de tu perfil de doctor. Contacta al soporte técnico.');
        }

        $dashboardData = $this->doctorService->getDashboardData($doctor);

        return Inertia::render('Doctors/Dashboard', [
            'doctor' => $doctor,
            'dashboardData' => $dashboardData,
        ]);
    }

    /**
     * Mostrar la página de gestión de emergencias.
     */
    public function emergency(Request $request)
    {
        // Verificar si el usuario tiene el rol de doctor
        if (!Auth::user()->hasRole('doctor')) {
            return redirect()->route('doctors.index')
                ->with('error', 'Acceso denegado: No tienes el rol de doctor asignado. Contacta al administrador para solicitar el acceso.');
        }
        
        $doctor = $this->doctorService->getCurrentDoctor();
        
        if (!$doctor) {
            return redirect()->route('doctors.index')
                ->with('error', 'Error en la configuración de tu perfil de doctor. Contacta al soporte técnico.');
        }

        // Obtener los tipos de citas marcados como emergencia
        $appointmentTypes = \Modules\Appointments\Models\AppointmentType::forClinic($doctor->clinic_id)
            ->where(function($query) {
                $query->emergency()
                      ->orWhere('name', 'like', '%emergencia%');
            })
            ->get();

        if ($appointmentTypes->isEmpty()) {
            // Si no hay tipos de cita específicos para emergencias, traer todos
            $appointmentTypes = \Modules\Appointments\Models\AppointmentType::forClinic($doctor->clinic_id)
                ->active()
                ->get();
        }

        return Inertia::render('Doctors/Emergency', [
            'doctor' => $doctor,
            'appointmentTypes' => $appointmentTypes,
        ]);
    }
}