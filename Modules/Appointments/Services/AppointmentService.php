<?php

namespace Modules\Appointments\Services;

use Illuminate\Support\Facades\DB;
use Modules\Appointments\Models\Appointment;
use Modules\Appointments\Models\AppointmentType;
use Modules\Patients\Models\Patient;
use Exception;
use Carbon\Carbon;

class AppointmentService
{
    /**
     * Get appointments for a specific clinic with filters.
     *
     * @param int $clinicId
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAppointments($clinicId, array $filters = [])
    {
        $query = Appointment::forClinic($clinicId)
            ->with(['patient', 'type']);
        
        // Apply date filter
        if (isset($filters['date'])) {
            $date = Carbon::parse($filters['date']);
            $query->whereDate('start_time', $date);
        }
        
        // Apply status filter
        if (isset($filters['status'])) {
            $query->withStatus($filters['status']);
        }
        
        // Apply doctor filter
        if (isset($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }
        
        // Apply patient filter
        if (isset($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }
        
        // Apply search term
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('expedient_number', 'like', "%{$search}%");
            });
        }
        
        // Default sort by start_time
        return $query->orderBy('start_time', 'asc')
                     ->paginate($filters['per_page'] ?? 15);
    }
    
    /**
     * Create a new appointment.
     *
     * @param array $data
     * @return Appointment
     */
    public function createAppointment(array $data)
    {
        DB::beginTransaction();
        
        try {
            // Calcular end_time basado en la duración si no se proporciona directamente
            if (!isset($data['end_time']) && isset($data['duration'])) {
                $startTime = Carbon::parse($data['start_time']);
                $data['end_time'] = $startTime->copy()->addMinutes($data['duration']);
            }
            
            // Verificar disponibilidad
            $this->checkAvailability(
                $data['clinic_id'],
                $data['doctor_id'] ?? null,
                $data['start_time'],
                $data['end_time']
            );
            
            // Crear la cita
            $appointment = Appointment::create($data);
            
            // Agregar terapias si existen
            if (isset($data['therapies']) && is_array($data['therapies'])) {
                foreach ($data['therapies'] as $therapy) {
                    $appointment->therapies()->attach($therapy['id'], [
                        'price' => $therapy['price'] ?? 0,
                        'duration' => $therapy['duration'] ?? 30,
                        'therapist_id' => $therapy['therapist_id'] ?? null,
                    ]);
                }
                
                // Actualizar precio total
                $appointment->update([
                    'total_price' => $appointment->therapies->sum('pivot.price')
                ]);
            }
            
            DB::commit();
            return $appointment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Update an existing appointment.
     *
     * @param Appointment $appointment
     * @param array $data
     * @return Appointment
     */
    public function updateAppointment(Appointment $appointment, array $data)
    {
        DB::beginTransaction();
        
        try {
            // Calcular end_time basado en la duración si no se proporciona directamente
            if (!isset($data['end_time']) && isset($data['duration'])) {
                $startTime = Carbon::parse($data['start_time'] ?? $appointment->start_time);
                $data['end_time'] = $startTime->copy()->addMinutes($data['duration']);
            }
            
            // Verificar disponibilidad solo si hay cambio de horario
            if (isset($data['start_time']) || isset($data['end_time'])) {
                $this->checkAvailability(
                    $appointment->clinic_id,
                    $data['doctor_id'] ?? $appointment->doctor_id,
                    $data['start_time'] ?? $appointment->start_time,
                    $data['end_time'] ?? $appointment->end_time,
                    $appointment->id
                );
            }
            
            // Actualizar la cita
            $appointment->update($data);
            
            // Actualizar terapias si existen
            if (isset($data['therapies']) && is_array($data['therapies'])) {
                // Eliminar relaciones existentes
                $appointment->therapies()->detach();
                
                // Agregar nuevas relaciones
                foreach ($data['therapies'] as $therapy) {
                    $appointment->therapies()->attach($therapy['id'], [
                        'price' => $therapy['price'] ?? 0,
                        'duration' => $therapy['duration'] ?? 30,
                        'therapist_id' => $therapy['therapist_id'] ?? null,
                    ]);
                }
                
                // Actualizar precio total
                $appointment->update([
                    'total_price' => $appointment->therapies->sum('pivot.price')
                ]);
            }
            
            DB::commit();
            return $appointment;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Delete an appointment.
     *
     * @param Appointment $appointment
     * @return bool
     */
    public function deleteAppointment(Appointment $appointment)
    {
        return $appointment->delete();
    }
    
    /**
     * Check if a time slot is available.
     *
     * @param int $clinicId
     * @param int|null $doctorId
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeAppointmentId
     * @return bool
     * @throws Exception
     */
    private function checkAvailability($clinicId, $doctorId, $startTime, $endTime, $excludeAppointmentId = null)
    {
        $startTime = Carbon::parse($startTime);
        $endTime = Carbon::parse($endTime);
        
        // Verificar que la fecha de inicio sea anterior a la fecha de fin
        if ($startTime->greaterThanOrEqualTo($endTime)) {
            throw new Exception('La fecha de inicio debe ser anterior a la fecha de fin.');
        }
        
        // Si no hay doctor asignado, no hay conflicto con disponibilidad del doctor
        if (!$doctorId) {
            return true;
        }
        
        // Buscar citas que puedan causar conflicto
        $query = Appointment::forClinic($clinicId)
            ->where('doctor_id', $doctorId)
            ->where(function ($q) use ($startTime, $endTime) {
                // Citas que se superponen con el nuevo horario
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                       ->where('end_time', '>', $startTime);
                });
            });
        
        // Excluir la cita actual en caso de edición
        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }
        
        $conflictingAppointments = $query->count();
        
        if ($conflictingAppointments > 0) {
            throw new Exception('Existe un conflicto de horario con otra cita.');
        }
        
        // Verificar disponibilidad del doctor
        $availabilityService = app(AvailabilityService::class);
        if (!$availabilityService->isDoctorAvailable($doctorId, $clinicId, $startTime, $endTime)) {
            throw new Exception('El doctor no está disponible en el horario seleccionado.');
        }
        
        return true;
    }
    
    /**
     * Get statistics for appointments.
     *
     * @param int $clinicId
     * @param string $period current_week, current_month, current_year
     * @return array
     */
    public function getStatistics($clinicId, $period = 'current_month')
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'current_week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'current_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'current_year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
        }
        
        $total = Appointment::forClinic($clinicId)
            ->whereBetween('start_time', [$start, $end])
            ->count();
            
        $scheduled = Appointment::forClinic($clinicId)
            ->whereBetween('start_time', [$start, $end])
            ->withStatus('scheduled')
            ->count();
            
        $completed = Appointment::forClinic($clinicId)
            ->whereBetween('start_time', [$start, $end])
            ->withStatus('completed')
            ->count();
            
        $cancelled = Appointment::forClinic($clinicId)
            ->whereBetween('start_time', [$start, $end])
            ->withStatus('cancelled')
            ->count();
            
        $noShow = Appointment::forClinic($clinicId)
            ->whereBetween('start_time', [$start, $end])
            ->withStatus('no-show')
            ->count();
            
        $totalRevenue = Appointment::forClinic($clinicId)
            ->whereBetween('start_time', [$start, $end])
            ->withStatus('completed')
            ->sum('total_price');
            
        return [
            'period' => $period,
            'total' => $total,
            'scheduled' => $scheduled,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'no_show' => $noShow,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'cancellation_rate' => $total > 0 ? round(($cancelled / $total) * 100, 2) : 0,
            'no_show_rate' => $total > 0 ? round(($noShow / $total) * 100, 2) : 0,
            'total_revenue' => $totalRevenue,
        ];
    }
}