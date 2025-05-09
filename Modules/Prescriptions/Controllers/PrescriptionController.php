<?php

namespace Modules\Prescriptions\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Prescriptions\Models\Prescription;
use Modules\Prescriptions\Services\PrescriptionService;
use Modules\Patients\Models\Patient;
use Modules\Doctors\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PrescriptionController extends Controller
{
    protected $prescriptionService;

    public function __construct(PrescriptionService $prescriptionService)
    {
        $this->prescriptionService = $prescriptionService;
    }

    /**
     * Display a listing of the prescriptions.
     */
    public function index(Request $request)
    {
        $prescriptions = Prescription::with(['patient', 'doctor'])
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->orWhereHas('doctor', function ($q) use ($search) {
                    $q->whereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    });
                })
                ->orWhere('prescription_number', 'like', "%{$search}%");
            })
            ->when($request->has('patient_id'), function ($query) use ($request) {
                $query->where('patient_id', $request->input('patient_id'));
            })
            ->when($request->has('doctor_id'), function ($query) use ($request) {
                $query->where('doctor_id', $request->input('doctor_id'));
            })
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Prescriptions/Index', [
            'prescriptions' => $prescriptions,
            'filters' => $request->only(['search', 'patient_id', 'doctor_id', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new prescription.
     */
    public function create(Request $request)
    {
        $patients = Patient::select('id', 'name', 'last_name')
            ->orderBy('name')
            ->get();

        $doctors = Doctor::with('user:id,name')
            ->orderBy('id')
            ->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->name,
                    'specialty' => $doctor->specialty,
                ];
            });

        // If patient_id is provided in the query, get that patient
        $selectedPatient = null;
        if ($request->has('patient_id')) {
            $selectedPatient = Patient::find($request->input('patient_id'));
        }

        // If doctor_id is provided in the query, get that doctor
        $selectedDoctor = null;
        if ($request->has('doctor_id')) {
            $selectedDoctor = Doctor::with('user:id,name')->find($request->input('doctor_id'));
        }

        return Inertia::render('Prescriptions/Create', [
            'patients' => $patients,
            'doctors' => $doctors,
            'selectedPatient' => $selectedPatient,
            'selectedDoctor' => $selectedDoctor,
        ]);
    }

    /**
     * Store a newly created prescription in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'diagnosis' => 'nullable|string',
            'instructions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.posology' => 'nullable|string',
            'items.*.instructions' => 'nullable|string',
            'items.*.is_supplement' => 'nullable|boolean',
            'items.*.supplement_id' => 'nullable|exists:supplements,id',
            'items.*.dosage' => 'nullable|string|max:255',
            'items.*.frequency' => 'nullable|string|max:255',
            'items.*.duration' => 'nullable|string|max:255',
            'items.*.notes' => 'nullable|string',
        ]);

        $prescriptionData = $request->except(['items']);
        $prescriptionData['clinic_id'] = session('clinic_id', 1); // Default to clinic_id 1 if not set
        $prescriptionData['status'] = 'active';
        $prescriptionData['notification_sent'] = false;

        $itemsData = $request->input('items');

        $prescription = $this->prescriptionService->createPrescription(
            $prescriptionData,
            $itemsData
        );

        return redirect()->route('prescriptions.show', $prescription)
            ->with('success', 'Receta creada exitosamente.');
    }

    /**
     * Display the specified prescription.
     */
    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor.user', 'items']);

        return Inertia::render('Prescriptions/Show', [
            'prescription' => $prescription,
        ]);
    }

    /**
     * Show the form for editing the specified prescription.
     */
    public function edit(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor.user', 'items']);

        $patients = Patient::select('id', 'name', 'last_name')
            ->orderBy('name')
            ->get();

        $doctors = Doctor::with('user:id,name')
            ->orderBy('id')
            ->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->name,
                    'specialty' => $doctor->specialty,
                ];
            });

        return Inertia::render('Prescriptions/Edit', [
            'prescription' => $prescription,
            'patients' => $patients,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Update the specified prescription in storage.
     */
    public function update(Request $request, Prescription $prescription)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'diagnosis' => 'nullable|string',
            'instructions' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.posology' => 'nullable|string',
            'items.*.instructions' => 'nullable|string',
            'items.*.is_supplement' => 'nullable|boolean',
            'items.*.supplement_id' => 'nullable|exists:supplements,id',
            'items.*.dosage' => 'nullable|string|max:255',
            'items.*.frequency' => 'nullable|string|max:255',
            'items.*.duration' => 'nullable|string|max:255',
            'items.*.notes' => 'nullable|string',
        ]);

        $prescriptionData = $request->except(['items']);
        $itemsData = $request->input('items');

        $prescription = $this->prescriptionService->updatePrescription(
            $prescription,
            $prescriptionData,
            $itemsData
        );

        return redirect()->route('prescriptions.show', $prescription)
            ->with('success', 'Receta actualizada exitosamente.');
    }

    /**
     * Remove the specified prescription from storage.
     */
    public function destroy(Prescription $prescription)
    {
        $prescription->delete();

        return redirect()->route('prescriptions.index')
            ->with('success', 'Receta eliminada exitosamente.');
    }

    /**
     * Generate a PDF for the specified prescription.
     */
    public function generatePdf(Prescription $prescription)
    {
        $pdfPath = $this->prescriptionService->generatePdf($prescription);

        // This would likely be handled differently in a real implementation
        // Maybe returning a file download response or a URL
        return response()->json(['pdf_url' => $pdfPath]);
    }

    /**
     * Send notification about the prescription to the patient.
     */
    public function sendNotification(Prescription $prescription)
    {
        $success = $this->prescriptionService->sendNotification($prescription);

        if ($success) {
            return back()->with('success', 'Notificación enviada exitosamente.');
        }

        return back()->with('error', 'Error al enviar la notificación.');
    }
}