<?php

namespace Modules\Therapies\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Modules\Therapies\Models\Therapy;
use Modules\Therapies\Services\TherapyService;

class TherapyController extends Controller
{
    protected $therapyService;

    public function __construct(TherapyService $therapyService)
    {
        $this->therapyService = $therapyService;
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of therapies.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', Therapy::class);
        
        $filters = $request->all();
        $clinicId = $request->user()->clinic_id;
        
        $therapies = $this->therapyService->getTherapies($clinicId, $filters);
        
        return Inertia::render('Therapies/Index', [
            'therapies' => $therapies,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new therapy.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        // $this->authorize('create', Therapy::class);
        
        return Inertia::render('Therapies/Create');
    }

    /**
     * Store a newly created therapy in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // $this->authorize('create', Therapy::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_price' => 'required|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'default_duration' => 'required|integer|min:1',
            'media' => 'nullable|array',
            'status' => 'nullable|string|in:active,inactive',
        ]);
        
        $data = array_merge($validated, [
            'clinic_id' => $request->user()->clinic_id,
            'created_by' => $request->user()->id,
            'status' => $validated['status'] ?? 'active',
        ]);
        
        $therapy = $this->therapyService->createTherapy($data);
        
        return redirect()->route('therapies.show', $therapy)
            ->with('success', 'Terapia creada exitosamente.');
    }

    /**
     * Display the specified therapy.
     *
     * @param Therapy $therapy
     * @return \Inertia\Response
     */
    public function show(Therapy $therapy)
    {
        // $this->authorize('view', $therapy);
        
        $therapy->load(['creator', 'instructions']);
        
        return Inertia::render('Therapies/Show', [
            'therapy' => $therapy
        ]);
    }

    /**
     * Show the form for editing the specified therapy.
     *
     * @param Therapy $therapy
     * @return \Inertia\Response
     */
    public function edit(Therapy $therapy)
    {
        // $this->authorize('update', $therapy);
        
        return Inertia::render('Therapies/Edit', [
            'therapy' => $therapy
        ]);
    }

    /**
     * Update the specified therapy in storage.
     *
     * @param Request $request
     * @param Therapy $therapy
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Therapy $therapy)
    {
        // $this->authorize('update', $therapy);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'default_price' => 'required|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'default_duration' => 'required|integer|min:1',
            'media' => 'nullable|array',
            'status' => 'nullable|string|in:active,inactive',
        ]);
        
        $therapy = $this->therapyService->updateTherapy($therapy, $validated);
        
        return redirect()->route('therapies.show', $therapy)
            ->with('success', 'Terapia actualizada exitosamente.');
    }

    /**
     * Remove the specified therapy from storage.
     *
     * @param Therapy $therapy
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Therapy $therapy)
    {
        // $this->authorize('delete', $therapy);
        
        $this->therapyService->deleteTherapy($therapy);
        
        return redirect()->route('therapies.index')
            ->with('success', 'Terapia eliminada exitosamente.');
    }
}