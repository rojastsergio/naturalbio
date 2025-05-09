<?php

namespace Modules\Doctors\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Doctors\Models\Doctor;
use Modules\Doctors\Models\DoctorAvailability;
use Illuminate\Support\Facades\Auth;

class AvailabilityService
{
    /**
     * Obtener el doctor asociado al usuario actual.
     */
    public function getCurrentDoctor()
{
    if (!Auth::check()) {
        return null;
    }
    
    return Doctor::where('user_id', Auth::id())->first();
}

    /**
     * Obtener disponibilidades de un doctor, aplicando filtros si existen.
     */
    public function getAvailabilities(Doctor $doctor, Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today()->addDays(30);

        return DoctorAvailability::where('doctor_id', $doctor->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->whereNotNull('recurrence')
                          ->where('recurrence_end', '>=', $startDate);
                    });
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Crear una nueva disponibilidad.
     */
    public function createAvailability(array $data)
{
    if (!Auth::check()) {
        throw new \Exception('Usuario no autenticado');
    }
    
    $user = Auth::user();
    if ($user && isset($user->clinic_id)) {
        $data['clinic_id'] = $user->clinic_id;
    } else {
        throw new \Exception('El usuario no tiene una clínica asignada');
    }

    return DoctorAvailability::create($data);
}

    /**
     * Actualizar una disponibilidad existente.
     */
    public function updateAvailability(DoctorAvailability $availability, array $data)
    {
        return $availability->update($data);
    }

    /**
     * Eliminar una disponibilidad.
     */
    public function deleteAvailability(DoctorAvailability $availability)
    {
        return $availability->delete();
    }

    /**
     * Obtener disponibilidad para un rango de fechas.
     */
    public function getAvailabilityForDates($doctorId, $startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);
        
        // Obtener disponibilidades directas dentro del rango
        $directAvailabilities = DoctorAvailability::where('doctor_id', $doctorId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('active', true)
            ->get();
            
        // Obtener disponibilidades recurrentes que podrían afectar el rango
        $recurrentAvailabilities = DoctorAvailability::where('doctor_id', $doctorId)
            ->whereNotNull('recurrence')
            ->where('recurrence_end', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->where('active', true)
            ->get();
            
        // Procesar disponibilidades recurrentes para expandirlas en el rango
        $expandedAvailabilities = $this->expandRecurrentAvailabilities(
            $recurrentAvailabilities,
            $startDate,
            $endDate
        );
        
        // Combinar ambas colecciones
        return $directAvailabilities->concat($expandedAvailabilities);
    }
    
    /**
     * Expandir disponibilidades recurrentes para un rango de fechas.
     */
    private function expandRecurrentAvailabilities($recurrentAvailabilities, $startDate, $endDate)
    {
        $expanded = collect();
        
        foreach ($recurrentAvailabilities as $availability) {
            $baseDate = Carbon::parse($availability->date);
            $recurrenceEnd = Carbon::parse($availability->recurrence_end);
            $currentDate = clone $baseDate;
            
            // Ajustar la fecha actual si es anterior al inicio del rango
            if ($currentDate->lt($startDate)) {
                // Calcular cuántas recurrencias necesitamos avanzar
                switch ($availability->recurrence) {
                    case 'daily':
                        while ($currentDate->lt($startDate)) {
                            $currentDate->addDay();
                        }
                        break;
                    case 'weekly':
                        while ($currentDate->lt($startDate)) {
                            $currentDate->addWeek();
                        }
                        break;
                    case 'biweekly':
                        while ($currentDate->lt($startDate)) {
                            $currentDate->addWeeks(2);
                        }
                        break;
                    case 'monthly':
                        while ($currentDate->lt($startDate)) {
                            $currentDate->addMonth();
                        }
                        break;
                }
            }
            
            // Generar todas las ocurrencias dentro del rango
            while ($currentDate->lte($endDate) && $currentDate->lte($recurrenceEnd)) {
                // Clonar la disponibilidad con la nueva fecha
                $expandedAvailability = clone $availability;
                $expandedAvailability->date = $currentDate->toDateString();
                $expanded->push($expandedAvailability);
                
                // Avanzar al siguiente período
                switch ($availability->recurrence) {
                    case 'daily':
                        $currentDate->addDay();
                        break;
                    case 'weekly':
                        $currentDate->addWeek();
                        break;
                    case 'biweekly':
                        $currentDate->addWeeks(2);
                        break;
                    case 'monthly':
                        $currentDate->addMonth();
                        break;
                }
            }
        }
        
        return $expanded;
    }
}