<?php

namespace Modules\Supplements\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Supplements\Models\Supplement;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplementService
{
    /**
     * Obtener todos los suplementos con filtros.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAllSupplements(Request $request)
    {
        return Supplement::forClinic()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->has('price_min'), function ($query) use ($request) {
                $query->where('price', '>=', $request->input('price_min'));
            })
            ->when($request->has('price_max'), function ($query) use ($request) {
                $query->where('price', '<=', $request->input('price_max'));
            })
            ->orderBy($request->input('sort_by', 'created_at'), $request->input('sort_direction', 'desc'))
            ->paginate($request->input('per_page', 10))
            ->withQueryString();
    }

    /**
     * Crear un nuevo suplemento.
     *
     * @param array $data
     * @param array $mediaFiles
     * @return Supplement
     */
    public function createSupplement(array $data, array $mediaFiles = [])
    {
        $data['clinic_id'] = session('clinic_id', 1);

        // Procesar archivos multimedia
        $media = [];
        if (!empty($mediaFiles)) {
            foreach ($mediaFiles as $file) {
                $path = $file->store('supplements', 'public');
                $media[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
            $data['media'] = $media;
        }

        return Supplement::create($data);
    }

    /**
     * Actualizar un suplemento existente.
     *
     * @param Supplement $supplement
     * @param array $data
     * @param array $mediaFiles
     * @return Supplement
     */
    public function updateSupplement(Supplement $supplement, array $data, array $mediaFiles = [])
    {
        // Procesar nuevos archivos multimedia
        if (!empty($mediaFiles)) {
            $existingMedia = $supplement->media ?: [];
            
            foreach ($mediaFiles as $file) {
                $path = $file->store('supplements', 'public');
                $existingMedia[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
            
            $data['media'] = $existingMedia;
        }

        $supplement->update($data);
        return $supplement;
    }

    /**
     * Eliminar un archivo multimedia de un suplemento.
     *
     * @param Supplement $supplement
     * @param int $mediaIndex
     * @return bool
     */
    public function removeMedia(Supplement $supplement, int $mediaIndex)
    {
        $media = $supplement->media ?: [];
        
        if (isset($media[$mediaIndex])) {
            $mediaItem = $media[$mediaIndex];
            
            // Eliminar el archivo del almacenamiento
            if (isset($mediaItem['path'])) {
                Storage::disk('public')->delete($mediaItem['path']);
            }
            
            // Eliminar el elemento del array
            array_splice($media, $mediaIndex, 1);
            
            // Actualizar el campo media
            $supplement->update(['media' => $media]);
            
            return true;
        }
        
        return false;
    }

    /**
     * Eliminar un suplemento y sus archivos multimedia.
     *
     * @param Supplement $supplement
     * @return bool
     */
    public function deleteSupplement(Supplement $supplement)
    {
        // Eliminar archivos multimedia
        $media = $supplement->media ?: [];
        foreach ($media as $mediaItem) {
            if (isset($mediaItem['path'])) {
                Storage::disk('public')->delete($mediaItem['path']);
            }
        }
        
        return $supplement->delete();
    }
}