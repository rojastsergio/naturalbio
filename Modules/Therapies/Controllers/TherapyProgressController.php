<?php

namespace Modules\Therapies\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Therapies\Models\TherapyAssignment;
use Modules\Therapies\Models\TherapyProgress;
use Modules\Therapies\Services\TherapyProgressService;

class TherapyProgressController extends Controller
{
    protected $therapyProgressService;

    public function __construct(TherapyProgressService $therapyProgressService)
    {
        $this->therapyProgressService = $therapyProgressService;
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of therapy progresses for an assignment.
     *
     * @param TherapyAssignment $assignment
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(TherapyAssignment $assignment)
    {
        // $this->authorize('viewProgressFor', $assignment);
        
        $progresses = $assignment->progresses()
            ->with('recordedBy')
            ->orderBy('recorded_at', 'desc')
            ->get();
        
        return response()->json(['progresses' => $progresses]);
    }

    /**
     * Store a newly created therapy progress in storage.
     *
     * @param Request $request
     * @param TherapyAssignment $assignment
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, TherapyAssignment $assignment)
    {
        // $this->authorize('createProgressFor', $assignment);
        
        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'status' => 'required|string|in:not_started,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        $data = array_merge($validated, [
            'therapy_assignment_id' => $assignment->id,
            'recorded_by' => Auth::id(),
            'recorded_at' => now(),
        ]);
        
        $progress = $this->therapyProgressService->createProgress($data);
        
        // Update assignment status based on progress
        $this->therapyProgressService->updateAssignmentStatus($assignment, $validated['status']);
        
        return response()->json([
            'success' => true,
            'progress' => $progress,
            'message' => 'Progreso registrado exitosamente.'
        ]);
    }

    /**
     * Update the specified therapy progress in storage.
     *
     * @param Request $request
     * @param TherapyProgress $progress
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, TherapyProgress $progress)
    {
        // $this->authorize('update', $progress);
        
        $validated = $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'status' => 'required|string|in:not_started,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        $progress = $this->therapyProgressService->updateProgress($progress, $validated);
        
        // Update assignment status based on progress
        $assignment = $progress->therapyAssignment;
        $this->therapyProgressService->updateAssignmentStatus($assignment, $validated['status']);
        
        return response()->json([
            'success' => true,
            'progress' => $progress,
            'message' => 'Progreso actualizado exitosamente.'
        ]);
    }
}