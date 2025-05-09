<?php

namespace Modules\Appointments\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Appointments\Models\AppointmentType;
use Inertia\Inertia;

class AppointmentTypeController extends Controller
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
     * Display a listing of appointment types.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', AppointmentType::class);
        
        $clinicId = $request->user()->clinic_id;
        
        $appointmentTypes = AppointmentType::forClinic($clinicId)
            ->orderBy('name')
            ->paginate(15);
        
        return Inertia::render('Appointments/Types/Index', [
            'appointmentTypes' => $appointmentTypes
        ]);
    }
    
    /**
     * Show the form for creating a new appointment type.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->authorize('create', AppointmentType::class);
        
        return Inertia::render('Appointments/Types/Create');
    }
    
    /**
     * Store a newly created appointment type in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', AppointmentType::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'default_price' => 'nullable|numeric|min:0',
            'default_duration' => 'nullable|integer|min:1',
            'active' => 'boolean'
        ]);
        
        $appointmentType = AppointmentType::create(array_merge(
            $validated,
            ['clinic_id' => $request->user()->clinic_id]
        ));
        
        return redirect()->route('appointment-types.index')
            ->with('success', 'Tipo de cita creado exitosamente.');
    }
    
    /**
     * Show the form for editing the specified appointment type.
     *
     * @param AppointmentType $appointmentType
     * @return \Inertia\Response
     */
    public function edit(AppointmentType $appointmentType)
    {
        $this->authorize('update', $appointmentType);
        
        return Inertia::render('Appointments/Types/Edit', [
            'appointmentType' => $appointmentType
        ]);
    }
    
    /**
     * Update the specified appointment type in storage.
     *
     * @param Request $request
     * @param AppointmentType $appointmentType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, AppointmentType $appointmentType)
    {
        $this->authorize('update', $appointmentType);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
            'default_price' => 'nullable|numeric|min:0',
            'default_duration' => 'nullable|integer|min:1',
            'active' => 'boolean'
        ]);
        
        $appointmentType->update($validated);
        
        return redirect()->route('appointment-types.index')
            ->with('success', 'Tipo de cita actualizado exitosamente.');
    }
    
    /**
     * Remove the specified appointment type from storage.
     *
     * @param AppointmentType $appointmentType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(AppointmentType $appointmentType)
    {
        $this->authorize('delete', $appointmentType);
        
        $appointmentType->delete();
        
        return redirect()->route('appointment-types.index')
            ->with('success', 'Tipo de cita eliminado exitosamente.');
    }
}