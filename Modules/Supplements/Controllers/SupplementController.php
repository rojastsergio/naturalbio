<?php

namespace Modules\Supplements\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Supplements\Models\Supplement;
use Modules\Supplements\Services\SupplementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SupplementController extends Controller
{
    protected $supplementService;

    public function __construct(SupplementService $supplementService)
    {
        $this->supplementService = $supplementService;
    }

    /**
     * Mostrar una lista de suplementos.
     */
    public function index(Request $request)
    {
        $supplements = $this->supplementService->getAllSupplements($request);

        return Inertia::render('Supplements/Index', [
            'supplements' => $supplements,
            'filters' => $request->only(['search', 'status', 'price_min', 'price_max']),
        ]);
    }

    /**
     * Mostrar el formulario para crear un nuevo suplemento.
     */
    public function create()
    {
        return Inertia::render('Supplements/Create');
    }

    /**
     * Almacenar un nuevo suplemento en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'default_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
            'media.*' => 'nullable|file|max:10240', // 10MB máximo por archivo
        ]);

        $mediaFiles = $request->file('media') ?? [];
        $supplement = $this->supplementService->createSupplement(
            $validated,
            $mediaFiles
        );

        return redirect()->route('supplements.show', $supplement)
            ->with('success', 'Suplemento creado exitosamente.');
    }

    /**
     * Mostrar la información de un suplemento específico.
     */
    public function show(Supplement $supplement)
    {
        return Inertia::render('Supplements/Show', [
            'supplement' => $supplement,
        ]);
    }

    /**
     * Mostrar el formulario para editar un suplemento.
     */
    public function edit(Supplement $supplement)
    {
        return Inertia::render('Supplements/Edit', [
            'supplement' => $supplement,
        ]);
    }

    /**
     * Actualizar la información de un suplemento en la base de datos.
     */
    public function update(Request $request, Supplement $supplement)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'default_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
            'media.*' => 'nullable|file|max:10240', // 10MB máximo por archivo
        ]);

        $mediaFiles = $request->file('media') ?? [];
        $this->supplementService->updateSupplement(
            $supplement,
            $validated,
            $mediaFiles
        );

        return redirect()->route('supplements.show', $supplement)
            ->with('success', 'Suplemento actualizado exitosamente.');
    }

    /**
     * Eliminar un archivo multimedia de un suplemento.
     */
    public function removeMedia(Request $request, Supplement $supplement)
    {
        $request->validate([
            'index' => 'required|integer|min:0',
        ]);

        $success = $this->supplementService->removeMedia(
            $supplement,
            $request->input('index')
        );

        if ($success) {
            return back()->with('success', 'Archivo eliminado exitosamente.');
        }

        return back()->with('error', 'No se pudo eliminar el archivo.');
    }

    /**
     * Eliminar un suplemento de la base de datos.
     */
    public function destroy(Supplement $supplement)
    {
        $this->supplementService->deleteSupplement($supplement);

        return redirect()->route('supplements.index')
            ->with('success', 'Suplemento eliminado exitosamente.');
    }
}