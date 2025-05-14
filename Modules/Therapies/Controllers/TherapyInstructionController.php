<?php

namespace Modules\Therapies\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Therapies\Models\Therapy;
use Modules\Therapies\Models\TherapyInstruction;
use Modules\Doctors\Models\Doctor;

class TherapyInstructionController extends Controller
{
    /**
     * Display a listing of therapy instructions.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', TherapyInstruction::class);
        
        $clinicId = $request->user()->clinic_id;
        
        $instructions = TherapyInstruction::with(['therapy', 'doctor.user'])
            ->whereHas('therapy', function ($query) use ($clinicId) {
                $query->where('clinic_id', $clinicId);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('body_area', 'like', "%{$search}%");
                });
            })
            ->when($request->has('therapy_id'), function ($query) use ($request) {
                $query->where('therapy_id', $request->input('therapy_id'));
            })
            ->when($request->has('doctor_id'), function ($query) use ($request) {
                $query->where('doctor_id', $request->input('doctor_id'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        // Get therapies for filtering
        $therapies = Therapy::forClinic($clinicId)
            ->active()
            ->select('id', 'name')
            ->get();
        
        // Get doctors for filtering
        $doctors = Doctor::byClinic($clinicId)
            ->with('user:id,name')
            ->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->name,
                ];
            });
        
        return Inertia::render('Therapies/Instructions/Index', [
            'instructions' => $instructions,
            'filters' => $request->only(['search', 'therapy_id', 'doctor_id']),
            'therapies' => $therapies,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Show the form for creating a new therapy instruction.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        // $this->authorize('create', TherapyInstruction::class);
        
        $clinicId = $request->user()->clinic_id;
        
        // Get therapies for form
        $therapies = Therapy::forClinic($clinicId)
            ->active()
            ->select('id', 'name', 'description')
            ->get();
        
        // Get current doctor if user has doctor role
        $doctor = Doctor::where('user_id', Auth::id())->first();
        
        return Inertia::render('Therapies/Instructions/Create', [
            'therapies' => $therapies,
            'doctor' => $doctor,
            'therapy_id' => $request->input('therapy_id'),
        ]);
    }

    /**
     * Store a newly created therapy instruction in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // $this->authorize('create', TherapyInstruction::class);
        
        $validated = $request->validate([
            'therapy_id' => 'required|exists:therapies,id',
            'doctor_id' => 'required|exists:doctors,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'body_area' => 'nullable|string|max:255',
            'instructions' => 'nullable|array',
            'media' => 'nullable|array',
        ]);
        
        $validated['status'] = 'active';
        
        $instruction = TherapyInstruction::create($validated);

        return redirect()->route('therapies.therapy-instructions.show', $instruction)
            ->with('success', 'Instrucción terapéutica creada exitosamente.');
    }

    /**
     * Display the specified therapy instruction.
     *
     * @param TherapyInstruction $instruction
     * @return \Inertia\Response
     */
    public function show(TherapyInstruction $instruction)
    {
        // $this->authorize('view', $instruction);
        
        $instruction->load(['therapy', 'doctor.user']);
        
        return Inertia::render('Therapies/Instructions/Show', [
            'instruction' => $instruction
        ]);
    }

    /**
     * Show the form for editing the specified therapy instruction.
     *
     * @param Request $request
     * @param TherapyInstruction $instruction
     * @return \Inertia\Response
     */
    public function edit(Request $request, TherapyInstruction $instruction)
    {
        // $this->authorize('update', $instruction);
        
        $clinicId = $request->user()->clinic_id;
        
        $instruction->load(['therapy', 'doctor']);
        
        // Get therapies for form
        $therapies = Therapy::forClinic($clinicId)
            ->active()
            ->select('id', 'name', 'description')
            ->get();
        
        return Inertia::render('Therapies/Instructions/Edit', [
            'instruction' => $instruction,
            'therapies' => $therapies,
        ]);
    }

    /**
     * Update the specified therapy instruction in storage.
     *
     * @param Request $request
     * @param TherapyInstruction $instruction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TherapyInstruction $instruction)
    {
        // $this->authorize('update', $instruction);
        
        $validated = $request->validate([
            'therapy_id' => 'required|exists:therapies,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'body_area' => 'nullable|string|max:255',
            'instructions' => 'nullable|array',
            'media' => 'nullable|array',
            'status' => 'required|string|in:active,inactive',
        ]);
        
        $instruction->update($validated);

        return redirect()->route('therapies.therapy-instructions.show', $instruction)
            ->with('success', 'Instrucción terapéutica actualizada exitosamente.');
    }

    /**
     * Remove the specified therapy instruction from storage.
     *
     * @param TherapyInstruction $instruction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TherapyInstruction $instruction)
    {
        // $this->authorize('delete', $instruction);
        
        $instruction->delete();

        return redirect()->route('therapies.therapy-instructions.index')
            ->with('success', 'Instrucción terapéutica eliminada exitosamente.');
    }
}