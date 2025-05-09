<?php

namespace Modules\Doctors\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Doctors\Models\Doctor;
use Modules\Doctors\Models\DoctorAvailability;
use Modules\Doctors\Services\AvailabilityService;

class DoctorAvailabilityController extends Controller
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
        $this->middleware('auth');
        $this->middleware('permission:manage availabilities');
    }

    /**
     * Mostrar la página de disponibilidad del doctor.
     */
    public function index(Request $request, ?Doctor $doctor = null)
{
    if (!$doctor) {
        $doctor = $this->availabilityService->getCurrentDoctor();
        
        if (!$doctor) {
            return redirect()->route('doctors.index')
                ->with('error', 'No tienes un perfil de doctor configurado.');
        }
    }

    $availabilities = $this->availabilityService->getAvailabilities($doctor, $request);

    return Inertia::render('Doctors/Availability', [
        'doctor' => $doctor,
        'availabilities' => $availabilities,
        'filters' => $request->only(['start_date', 'end_date']),
    ]);
}

    /**
     * Almacenar una nueva disponibilidad.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'recurrence' => 'nullable|string|in:daily,weekly,biweekly,monthly',
            'recurrence_end' => 'nullable|date|required_with:recurrence|after:date',
            'active' => 'boolean',
        ]);

        $availability = $this->availabilityService->createAvailability($validated);

        return redirect()->back()->with('success', 'Disponibilidad guardada exitosamente.');
    }

    /**
     * Actualizar una disponibilidad existente.
     */
    public function update(Request $request, DoctorAvailability $availability)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'recurrence' => 'nullable|string|in:daily,weekly,biweekly,monthly',
            'recurrence_end' => 'nullable|date|required_with:recurrence|after:date',
            'active' => 'boolean',
        ]);

        $this->availabilityService->updateAvailability($availability, $validated);

        return redirect()->back()->with('success', 'Disponibilidad actualizada exitosamente.');
    }

    /**
     * Eliminar una disponibilidad.
     */
    public function destroy(DoctorAvailability $availability)
    {
        $this->availabilityService->deleteAvailability($availability);

        return redirect()->back()->with('success', 'Disponibilidad eliminada exitosamente.');
    }

    /**
     * Obtener disponibilidad para un rango de fechas (API).
     */
    public function getAvailabilityForDates(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $availabilities = $this->availabilityService->getAvailabilityForDates(
            $validated['doctor_id'],
            $validated['start_date'],
            $validated['end_date']
        );

        return response()->json($availabilities);
    }
}