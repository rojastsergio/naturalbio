<?php

namespace Modules\Doctors\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importamos explícitamente la fachada Auth
use Inertia\Inertia;
use Modules\Doctors\Models\Doctor;
use Modules\Doctors\Models\TherapyInstruction;

class TherapyInstructionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Solo aplicar el middleware de permisos a ciertas acciones que requieren privilegios adicionales
        $this->middleware('permission:manage therapy_instructions', ['only' => ['store', 'update', 'destroy']]);
    }

    /**
     * Mostrar la lista de instrucciones terapéuticas.
     */
    public function index(Request $request, ?Doctor $doctor = null) // Parámetro explícitamente nullable
    {
        if (!$doctor) {
            // Si no se proporciona un doctor, intentar obtener el doctor actual
            if (Auth::check()) {
                $doctor = Doctor::where('user_id', Auth::id())->first();
            }
            
            if (!$doctor) {
                return redirect()->route('doctors.index')
                    ->with('error', 'No tienes un perfil de doctor configurado.');
            }
        }

        $instructions = TherapyInstruction::byDoctor($doctor->id)
            ->when($request->search, function ($query, $search) {
                return $query->search($search);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Doctors/TherapyInstructions', [
            'doctor' => $doctor,
            'instructions' => $instructions,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Almacenar una nueva instrucción terapéutica.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'required|string',
            'body_areas' => 'nullable|array',
            'media' => 'nullable|array',
        ]);

        // Añadir clinic_id basado en el usuario autenticado o el doctor
        if (Auth::check() && Auth::user() && isset(Auth::user()->clinic_id)) {
            $validated['clinic_id'] = Auth::user()->clinic_id;
        } else {
            // Fallback o manejo de error
            throw new \Exception('El usuario no tiene una clínica asignada');
        }

        TherapyInstruction::create($validated);

        return redirect()->back()->with('success', 'Instrucción terapéutica creada exitosamente.');
    }

    /**
     * Mostrar una instrucción terapéutica específica.
     */
    public function show(TherapyInstruction $instruction)
    {
        $instruction->load('doctor.user');

        return Inertia::render('Doctors/TherapyInstructionShow', [
            'instruction' => $instruction,
        ]);
    }

    /**
     * Actualizar una instrucción terapéutica existente.
     */
    public function update(Request $request, TherapyInstruction $instruction)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'required|string',
            'body_areas' => 'nullable|array',
            'media' => 'nullable|array',
        ]);

        $instruction->update($validated);

        return redirect()->back()->with('success', 'Instrucción terapéutica actualizada exitosamente.');
    }

    /**
     * Eliminar una instrucción terapéutica.
     */
    public function destroy(TherapyInstruction $instruction)
    {
        $instruction->delete();

        return redirect()->back()->with('success', 'Instrucción terapéutica eliminada exitosamente.');
    }
}