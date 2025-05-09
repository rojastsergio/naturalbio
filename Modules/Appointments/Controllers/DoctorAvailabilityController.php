<?php

namespace Modules\Appointments\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Appointments\Models\DoctorAvailability;
use Inertia\Inertia;
use Carbon\Carbon;

class DoctorAvailabilityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }
    
    /**
     * Display a listing of doctor availabilities.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', DoctorAvailability::class);
        
        $clinicId = $request->user()->clinic_id;
        $doctorId = $request->input('doctor_id');
        
        // Si el usuario tiene rol de doctor, mostrar solo sus disponibilidades
        if ($request->user()->hasRole('doctor') && !$doctorId) {
            $doctorId = $request->user()->id;
        }
        
        $query = DoctorAvailability::forClinic($clinicId)
            ->when($doctorId, function ($query, $doctorId) {
                return $query->forDoctor($doctorId);
            })
            ->with('doctor')
            ->orderBy('date')
            ->orderBy('start_time');
            
        // Filtrar por fechas futuras si se especifica
        if ($request->boolean('future_only', true)) {
            $query->fromDate(now()->toDateString());
        }
        
        $availabilities = $query->paginate(15);
        
        // Obtener doctores para filtro
        $doctors = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'doctor');
            })
            ->where('clinic_id', $clinicId)
            ->select('id', 'name')
            ->get();
        
        return Inertia::render('Appointments/Availabilities/Index', [
            'availabilities' => $availabilities,
            'doctors' => $doctors,
            'filters' => $request->only(['doctor_id', 'future_only'])
        ]);
    }
    
    /**
     * Show the form for creating a new doctor availability.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', DoctorAvailability::class);
        
        $clinicId = $request->user()->clinic_id;
        
        // Si el usuario es un doctor, preseleccionarlo
        $doctorId = null;
        if ($request->user()->hasRole('doctor')) {
            $doctorId = $request->user()->id;
        }
        
        // Obtener doctores para el selector
        $doctors = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'doctor');
            })
            ->where('clinic_id', $clinicId)
            ->select('id', 'name')
            ->get();
            
        // Opciones de recurrencia
        $recurrenceOptions = [
            ['value' => null, 'label' => 'Sin recurrencia'],
            ['value' => 'daily', 'label' => 'Todos los días'],
            ['value' => 'weekly', 'label' => 'Semanalmente (mismo día de la semana)'],
            ['value' => 'monthly', 'label' => 'Mensualmente (mismo día del mes)'],
        ];
        
        return Inertia::render('Appointments/Availabilities/Create', [
            'doctors' => $doctors,
            'preselectedDoctorId' => $doctorId,
            'recurrenceOptions' => $recurrenceOptions,
            'defaultDate' => now()->toDateString(),
        ]);
    }
    
    /**
     * Store a newly created doctor availability in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', DoctorAvailability::class);
        
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'recurrence' => 'nullable|in:daily,weekly,monthly',
            'recurrence_end' => 'nullable|date_format:Y-m-d|required_with:recurrence|after:date',
            'active' => 'boolean'
        ]);
        
        // Verificar permiso para editar a otros doctores
        if (!$request->user()->hasRole('doctor') || $request->user()->id != $validated['doctor_id']) {
            $this->authorize('manageAny', DoctorAvailability::class);
        }
        
        DoctorAvailability::create(array_merge(
            $validated,
            ['clinic_id' => $request->user()->clinic_id]
        ));
        
        return redirect()->route('doctor-availabilities.index')
            ->with('success', 'Disponibilidad creada exitosamente.');
    }
    
    /**
     * Show the form for editing the specified doctor availability.
     *
     * @param DoctorAvailability $doctorAvailability
     * @return \Inertia\Response
     */
    public function edit(DoctorAvailability $doctorAvailability)
    {
        $this->authorize('update', $doctorAvailability);
        
        $doctorAvailability->load('doctor');
        
        // Formatear horas para formulario
        $startTime = Carbon::parse($doctorAvailability->start_time)->format('H:i');
        $endTime = Carbon::parse($doctorAvailability->end_time)->format('H:i');
        
        $availability = array_merge(
            $doctorAvailability->toArray(),
            ['start_time' => $startTime, 'end_time' => $endTime]
        );
        
        // Opciones de recurrencia
        $recurrenceOptions = [
            ['value' => null, 'label' => 'Sin recurrencia'],
            ['value' => 'daily', 'label' => 'Todos los días'],
            ['value' => 'weekly', 'label' => 'Semanalmente (mismo día de la semana)'],
            ['value' => 'monthly', 'label' => 'Mensualmente (mismo día del mes)'],
        ];
        
        // Obtener doctores para el selector
        $clinicId = $doctorAvailability->clinic_id;
        $doctors = \App\Models\User::whereHas('roles', function ($query) {
                $query->where('name', 'doctor');
            })
            ->where('clinic_id', $clinicId)
            ->select('id', 'name')
            ->get();
        
        return Inertia::render('Appointments/Availabilities/Edit', [
            'availability' => $availability,
            'doctors' => $doctors,
            'recurrenceOptions' => $recurrenceOptions,
        ]);
    }
    
    /**
     * Update the specified doctor availability in storage.
     *
     * @param Request $request
     * @param DoctorAvailability $doctorAvailability
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, DoctorAvailability $doctorAvailability)
    {
        $this->authorize('update', $doctorAvailability);
        
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'recurrence' => 'nullable|in:daily,weekly,monthly',
            'recurrence_end' => 'nullable|date_format:Y-m-d|required_with:recurrence|after:date',
            'active' => 'boolean'
        ]);
        
        // Verificar permiso para editar a otros doctores
        if (!$request->user()->hasRole('doctor') || $request->user()->id != $validated['doctor_id']) {
            $this->authorize('manageAny', DoctorAvailability::class);
        }
        
        $doctorAvailability->update($validated);
        
        return redirect()->route('doctor-availabilities.index')
            ->with('success', 'Disponibilidad actualizada exitosamente.');
    }
    
    /**
     * Remove the specified doctor availability from storage.
     *
     * @param DoctorAvailability $doctorAvailability
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, DoctorAvailability $doctorAvailability)
{
    $this->authorize('delete', $doctorAvailability);
    
    // Verificar permiso para eliminar disponibilidades de otros doctores
    if (!$request->user()->hasRole('doctor') || $request->user()->id != $doctorAvailability->doctor_id) {
        $this->authorize('manageAny', DoctorAvailability::class);
    }
    
    $doctorAvailability->delete();
    
    return redirect()->route('doctor-availabilities.index')
        ->with('success', 'Disponibilidad eliminada exitosamente.');
}
    
    /**
     * Bulk create doctor availabilities (for recurring patterns).
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkCreate(Request $request)
    {
        $this->authorize('create', DoctorAvailability::class);
        
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'dates' => 'required|array',
            'dates.*' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'active' => 'boolean'
        ]);
        
        // Verificar permiso para crear disponibilidades para otros doctores
        if (!$request->user()->hasRole('doctor') || $request->user()->id != $validated['doctor_id']) {
            $this->authorize('manageAny', DoctorAvailability::class);
        }
        
        $clinicId = $request->user()->clinic_id;
        $doctorId = $validated['doctor_id'];
        $startTime = $validated['start_time'];
        $endTime = $validated['end_time'];
        $active = $validated['active'] ?? true;
        
        foreach ($validated['dates'] as $date) {
            DoctorAvailability::create([
                'clinic_id' => $clinicId,
                'doctor_id' => $doctorId,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'active' => $active
            ]);
        }
        
        return redirect()->route('doctor-availabilities.index')
            ->with('success', 'Disponibilidades creadas exitosamente.');
    }
}