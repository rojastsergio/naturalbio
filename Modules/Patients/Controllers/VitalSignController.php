<?php

namespace Modules\Patients\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Patients\Models\Patient;
use Modules\Patients\Models\VitalSign;
use Illuminate\Support\Facades\Auth;

class VitalSignController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create vitals')->only(['store']);
        $this->middleware('permission:update vitals')->only(['update']);
        $this->middleware('permission:delete vitals')->only(['destroy']);
    }

    /**
     * Store a newly created vital sign record in storage.
     */
    public function store(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'blood_pressure' => 'nullable|string',
            'oxygen_saturation' => 'nullable|numeric|min:0|max:100',
            'blood_glucose' => 'nullable|numeric|min:0',
            'heart_rate' => 'nullable|integer|min:0',
            'respiratory_rate' => 'nullable|integer|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'recorded_at' => 'nullable|date',
        ]);

        $validated['patient_id'] = $patient->id;
        $validated['recorded_by'] = Auth::id();
        $validated['recorded_at'] = $validated['recorded_at'] ?? now();

        VitalSign::create($validated);

        return redirect()->back()->with('success', 'Signos vitales registrados exitosamente.');
    }

    /**
     * Update the specified vital sign record in storage.
     */
    public function update(Request $request, VitalSign $vitalSign)
    {
        $validated = $request->validate([
            'blood_pressure' => 'nullable|string',
            'oxygen_saturation' => 'nullable|numeric|min:0|max:100',
            'blood_glucose' => 'nullable|numeric|min:0',
            'heart_rate' => 'nullable|integer|min:0',
            'respiratory_rate' => 'nullable|integer|min:0',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'recorded_at' => 'nullable|date',
        ]);

        $vitalSign->update($validated);

        return redirect()->back()->with('success', 'Signos vitales actualizados exitosamente.');
    }

    /**
     * Remove the specified vital sign record from storage.
     */
    public function destroy(VitalSign $vitalSign)
    {
        $vitalSign->delete();

        return redirect()->back()->with('success', 'Registro de signos vitales eliminado exitosamente.');
    }
}