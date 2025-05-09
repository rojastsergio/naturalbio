<?php

namespace Modules\Therapies\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Therapies\Models\TherapyAssignment;
use Modules\Therapies\Services\TherapyAssignmentService;
use App\Models\User;

class TherapyAssignmentController extends Controller
{
    protected $therapyAssignmentService;

    public function __construct(TherapyAssignmentService $therapyAssignmentService)
    {
        $this->therapyAssignmentService = $therapyAssignmentService;
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of therapy assignments.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', TherapyAssignment::class);
        
        $filters = $request->all();
        $therapist_id = $request->input('therapist_id') ?? Auth::id();
        
        $assignments = $this->therapyAssignmentService->getAssignments($therapist_id, $filters);
        
        // Get therapists (users with therapist role) for filtering
        $therapists = User::whereHas('roles', function ($query) {
                $query->where('name', 'therapist');
            })
            ->where('clinic_id', Auth::user()->clinic_id)
            ->select('id', 'name')
            ->get();
        
        return Inertia::render('Therapies/Assignments/Index', [
            'assignments' => $assignments,
            'filters' => $filters,
            'therapists' => $therapists,
        ]);
    }

    /**
     * Display the specified therapy assignment.
     *
     * @param TherapyAssignment $assignment
     * @return \Inertia\Response
     */
    public function show(TherapyAssignment $assignment)
    {
        // $this->authorize('view', $assignment);
        
        $assignment->load([
            'therapy', 
            'therapist', 
            'appointment.patient',
            'progresses' => function ($query) {
                $query->orderBy('recorded_at', 'desc');
            },
            'progresses.recordedBy'
        ]);
        
        return Inertia::render('Therapies/Assignments/Show', [
            'assignment' => $assignment
        ]);
    }

    /**
     * Update the specified therapy assignment in storage.
     *
     * @param Request $request
     * @param TherapyAssignment $assignment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TherapyAssignment $assignment)
    {
        // $this->authorize('update', $assignment);
        
        $validated = $request->validate([
            'therapist_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string|in:pending,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        $assignment = $this->therapyAssignmentService->updateAssignment($assignment, $validated);
        
        return redirect()->route('therapy-assignments.show', $assignment)
            ->with('success', 'Asignación de terapia actualizada exitosamente.');
    }
}