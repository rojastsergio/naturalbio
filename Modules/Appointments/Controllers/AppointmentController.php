<?php

namespace Modules\Appointments\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Modules\Appointments\Services\AppointmentService;
use Modules\Appointments\Services\AvailabilityService;
use Modules\Appointments\Models\Appointment;
use Modules\Appointments\Models\AppointmentType;
use Modules\Patients\Models\Patient;
use Modules\Therapies\Models\Therapy;
use App\Models\User;
use Inertia\Inertia;
use Exception;

class AppointmentController extends Controller
{
    protected $appointmentService;
    protected $availabilityService;
    
    /**
     * Create a new controller instance.
     *
     * @param AppointmentService $appointmentService
     * @param AvailabilityService $availabilityService
     * @return void
     */
    public function __construct(
        AppointmentService $appointmentService,
        AvailabilityService $availabilityService
    ) {
        $this->appointmentService = $appointmentService;
        $this->availabilityService = $availabilityService;
        $this->middleware(['auth']);
    }
    
    /**
     * Display a listing of appointments.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
{
    // $this->authorize('viewAny', Appointment::class);
    
    $filters = $request->all();
    $clinicId = $request->user()->clinic_id;
    
    // Check if we're requesting calendar format
    if ($request->input('format') === 'calendar') {
        $allAppointments = $this->appointmentService->getAppointmentsForCalendar($clinicId, $filters);
        return response()->json(['appointments' => $allAppointments]);
    }
    
    $appointments = $this->appointmentService->getAppointments($clinicId, $filters);
    
    // Obtener tipos de cita para el filtro
    $appointmentTypes = AppointmentType::forClinic($clinicId)
        ->active()
        ->select('id', 'name', 'color')
        ->get();
    
    // Estadísticas básicas
    $statistics = $this->appointmentService->getStatistics($clinicId);
    
    // Obtener doctores para el filtro
    $doctors = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'doctor');
        })
        ->where('clinic_id', $clinicId)
        ->select('id', 'name')
        ->get();
    
    return Inertia::render('Appointments/Index', [
        'appointments' => $appointments,
        'filters' => $filters,
        'appointmentTypes' => $appointmentTypes,
        'statistics' => $statistics,
        'doctors' => $doctors,
    ]);
}
    
    /**
     * Show the form for creating a new appointment.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
       // $this->authorize('create', Appointment::class);

        $clinicId = $request->user()->clinic_id;

        // Obtener tipos de cita
        $appointmentTypes = AppointmentType::forClinic($clinicId)
            ->active()
            ->select('id', 'name', 'color', 'default_duration', 'default_price')
            ->get();

        // Obtener paciente si hay un ID en la URL
        $patient = null;
        if ($request->has('patient_id')) {
            $patient = Patient::findOrFail($request->input('patient_id'));
        }

        // Obtener doctores (usuarios con rol doctor)
        $doctors = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'doctor');
            })
            ->where('clinic_id', $clinicId)
            ->select('id', 'name')
            ->get();

        // Obtener terapias disponibles
        $therapies = \Modules\Therapies\Models\Therapy::forClinic($clinicId)
            ->active()
            ->select('id', 'name', 'description', 'default_price', 'default_duration')
            ->get();

        // Obtener terapeutas (usuarios con rol de terapeuta)
        $therapists = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'therapist');
            })
            ->where('clinic_id', $clinicId)
            ->select('id', 'name')
            ->get();

        return Inertia::render('Appointments/Create', [
            'appointmentTypes' => $appointmentTypes,
            'patient' => $patient,
            'doctors' => $doctors,
            'therapies' => $therapies,
            'therapists' => $therapists,
            'defaultDate' => now()->format('Y-m-d'),
        ]);
    }
    
    /**
     * Store a newly created appointment in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        //$this->authorize('create', Appointment::class);
        
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'appointment_type_id' => 'nullable|exists:appointment_types,id',
            'start_time' => 'required|date',
            'duration' => 'required_without:end_time|integer|min:1',
            'end_time' => 'required_without:duration|date|after:start_time',
            'notes' => 'nullable|string',
            'therapies' => 'nullable|array',
            'therapies.*.id' => 'required|exists:therapies,id',
            'therapies.*.price' => 'required|numeric|min:0',
            'therapies.*.duration' => 'required|integer|min:1',
            'therapies.*.therapist_id' => 'nullable|exists:users,id',
        ]);
        
        try {
            $data = array_merge($validated, [
                'clinic_id' => $request->user()->clinic_id,
                'created_by' => $request->user()->id,
                'status' => 'scheduled'
            ]);
            
            $appointment = $this->appointmentService->createAppointment($data);
            
            return redirect()->route('appointments.show', $appointment)
                ->with('success', 'Cita creada exitosamente.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Display the specified appointment.
     *
     * @param Appointment $appointment
     * @return \Inertia\Response
     */
    public function show(Appointment $appointment)
    {
        //$this->authorize('view', $appointment);
        
        $appointment->load([
            'patient', 
            'type', 
            'doctor', 
            'therapies',
            'creator'
        ]);
        
        return Inertia::render('Appointments/Show', [
            'appointment' => $appointment
        ]);
    }
    
    /**
     * Show the form for editing the specified appointment.
     *
     * @param Appointment $appointment
     * @return \Inertia\Response
     */
    public function edit(Appointment $appointment)
    {
        //$this->authorize('update', $appointment);
        
        $appointment->load(['patient', 'type', 'doctor', 'therapies']);
        
        $clinicId = $appointment->clinic_id;
        
        // Obtener tipos de cita
        $appointmentTypes = AppointmentType::forClinic($clinicId)
            ->active()
            ->select('id', 'name', 'color', 'default_duration', 'default_price')
            ->get();
        
        // Obtener doctores (usuarios con rol doctor)
        $doctors = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'doctor');
            })
            ->where('clinic_id', $clinicId)
            ->select('id', 'name')
            ->get();
        
        // Calcular duración actual
        $startTime = \Carbon\Carbon::parse($appointment->start_time);
        $endTime = \Carbon\Carbon::parse($appointment->end_time);
        $duration = $endTime->diffInMinutes($startTime);
        
        return Inertia::render('Appointments/Edit', [
            'appointment' => array_merge($appointment->toArray(), ['duration' => $duration]),
            'appointmentTypes' => $appointmentTypes,
            'doctors' => $doctors,
        ]);
    }
    
    /**
     * Update the specified appointment in storage.
     *
     * @param Request $request
     * @param Appointment $appointment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Appointment $appointment)
    {
        //$this->authorize('update', $appointment);
        
        $validated = $request->validate([
            'doctor_id' => 'nullable|exists:users,id',
            'appointment_type_id' => 'nullable|exists:appointment_types,id',
            'start_time' => 'required|date',
            'duration' => 'required_without:end_time|integer|min:1',
            'end_time' => 'required_without:duration|date|after:start_time',
            'notes' => 'nullable|string',
            'doctor_notes' => 'nullable|string',
            'status' => 'nullable|in:scheduled,completed,cancelled,no-show',
            'therapies' => 'nullable|array',
            'therapies.*.id' => 'required|exists:therapies,id',
            'therapies.*.price' => 'required|numeric|min:0',
            'therapies.*.duration' => 'required|integer|min:1',
            'therapies.*.therapist_id' => 'nullable|exists:users,id',
        ]);
        
        try {
            $appointment = $this->appointmentService->updateAppointment($appointment, $validated);
            
            return redirect()->route('appointments.show', $appointment)
                ->with('success', 'Cita actualizada exitosamente.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Remove the specified appointment from storage.
     *
     * @param Appointment $appointment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Appointment $appointment)
    {
       // $this->authorize('delete', $appointment);
        
        try {
            $this->appointmentService->deleteAppointment($appointment);
            
            return redirect()->route('appointments.index')
                ->with('success', 'Cita eliminada exitosamente.');
        } catch (Exception $e) {
            return redirect()->back()->withErrors([
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get available time slots for a specific doctor and date.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function availability(Request $request)
    {
       // $this->authorize('viewAny', Appointment::class);
        
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date_format:Y-m-d',
            'duration' => 'required|integer|min:1'
        ]);
        
        $clinicId = $request->user()->clinic_id;
        
        $slots = $this->availabilityService->getAvailableTimeSlots(
            $validated['doctor_id'],
            $clinicId,
            $validated['date'],
            $validated['duration']
        );
        
        return response()->json(['slots' => $slots]);
    }
    
    /**
     * Send a reminder for the appointment.
     *
     * @param Request $request
     * @param Appointment $appointment
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendReminder(Request $request, Appointment $appointment)
    {
        $this->authorize('sendReminder', $appointment);

        // Esta función se implementará cuando se agregue la funcionalidad de notificaciones
        // Por ahora, solo retornamos un mensaje de éxito simulado

        return response()->json([
            'success' => true,
            'message' => 'Recordatorio enviado exitosamente.'
        ]);
    }

    /**
     * Create an emergency appointment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeEmergency(Request $request)
    {
        // Validar la solicitud
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_type_id' => 'required|exists:appointment_types,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'notes' => 'required|string',
        ]);

        // Marcar como emergencia
        $validated['emergency'] = true;
        $validated['status'] = 'scheduled';
        $validated['clinic_id'] = $request->user()->clinic_id;
        $validated['created_by'] = $request->user()->id;

        try {
            // Crear la cita de emergencia
            $appointment = $this->appointmentService->createAppointment($validated);

            return response()->json([
                'success' => true,
                'message' => 'La cita de emergencia fue registrada exitosamente.',
                'id' => $appointment->id
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}