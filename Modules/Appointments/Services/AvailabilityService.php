<?php

namespace Modules\Appointments\Services;

use Modules\Appointments\Models\DoctorAvailability;
use Carbon\Carbon;
use Exception;

class AvailabilityService
{
    /**
     * Check if a doctor is available in a specific time slot.
     *
     * @param int $doctorId
     * @param int $clinicId
     * @param Carbon|string $startTime
     * @param Carbon|string $endTime
     * @return bool
     */
    public function isDoctorAvailable($doctorId, $clinicId, $startTime, $endTime)
    {
        // Si no se proporciona un doctor_id, asumimos que está disponible
        if (!$doctorId) {
            return true;
        }

        $startTime = Carbon::parse($startTime);
        $endTime = Carbon::parse($endTime);
        $date = $startTime->toDateString();

        // Buscar disponibilidades para el doctor y la clínica en la fecha dada
        $availabilities = DoctorAvailability::forClinic($clinicId)
            ->forDoctor($doctorId)
            ->active()
            ->where(function ($query) use ($date) {
                // Disponibilidades para la fecha específica
                $query->where('date', $date);

                // O disponibilidades recurrentes que incluyan la fecha
                $query->orWhere(function ($q) use ($date) {
                    $dateObj = Carbon::parse($date);

                    // Recurrencia diaria
                    $q->where('recurrence', 'daily')
                      ->where('date', '<=', $date)
                      ->where(function ($q2) use ($date) {
                          $q2->whereNull('recurrence_end')
                             ->orWhere('recurrence_end', '>=', $date);
                      });
                });

                // Recurrencia semanal (mismo día de la semana)
                $query->orWhere(function ($q) use ($date) {
                    $dateObj = Carbon::parse($date);
                    $dayOfWeek = $dateObj->dayOfWeek;

                    $q->where('recurrence', 'weekly')
                      ->whereRaw("DAYOFWEEK(date) = ?", [$dayOfWeek + 1]) // MySQL DAYOFWEEK() es 1-7 (domingo-sábado)
                      ->where('date', '<=', $date)
                      ->where(function ($q2) use ($date) {
                          $q2->whereNull('recurrence_end')
                             ->orWhere('recurrence_end', '>=', $date);
                      });
                });

                // Recurrencia mensual (mismo día del mes)
                $query->orWhere(function ($q) use ($date) {
                    $dateObj = Carbon::parse($date);
                    $dayOfMonth = $dateObj->day;

                    $q->where('recurrence', 'monthly')
                      ->whereRaw("DAY(date) = ?", [$dayOfMonth])
                      ->where('date', '<=', $date)
                      ->where(function ($q2) use ($date) {
                          $q2->whereNull('recurrence_end')
                             ->orWhere('recurrence_end', '>=', $date);
                      });
                });
            })
            ->get();

        // Si no hay disponibilidades configuradas, consideramos al doctor como disponible
        // Esto nos permite crear citas sin tener que configurar horarios
        if ($availabilities->isEmpty()) {
            return true;
        }

        // Verificar si alguna de las disponibilidades cubre todo el rango horario
        foreach ($availabilities as $availability) {
            // Convertir el tiempo de disponibilidad a objetos DateTime correctamente formateados
            $availStartTime = Carbon::parse($availability->start_time)->format('H:i:s');
            $availEndTime = Carbon::parse($availability->end_time)->format('H:i:s');

            $availStart = Carbon::parse($date . ' ' . $availStartTime);
            $availEnd = Carbon::parse($date . ' ' . $availEndTime);

            // Si la disponibilidad cubre todo el rango horario
            if ($availStart->lessThanOrEqualTo($startTime) && $availEnd->greaterThanOrEqualTo($endTime)) {
                return true;
            }
        }

        // Si hay disponibilidades configuradas pero ninguna cubre el rango solicitado
        return false;
    }
    
    /**
     * Get available time slots for a doctor on a specific date.
     *
     * @param int $doctorId
     * @param int $clinicId
     * @param string $date
     * @param int $duration Duration in minutes
     * @return array
     */
    public function getAvailableTimeSlots($doctorId, $clinicId, $date, $duration = 60)
    {
        $dateObj = Carbon::parse($date);
        
        // Obtener todas las disponibilidades para el doctor en la fecha
        $availabilities = $this->getDoctorAvailabilitiesForDate($doctorId, $clinicId, $date);
        
        // Si no hay disponibilidades, no hay slots disponibles
        if ($availabilities->isEmpty()) {
            return [];
        }
        
        // Obtener todas las citas del doctor para la fecha
        $appointments = $this->getDoctorAppointmentsForDate($doctorId, $clinicId, $date);
        
        $slots = [];
        
        // Para cada disponibilidad, generar slots
        foreach ($availabilities as $availability) {
            $startTime = Carbon::parse($date . ' ' . $availability->start_time);
            $endTime = Carbon::parse($date . ' ' . $availability->end_time);
            
            // Generar slots cada $duration minutos
            $current = $startTime->copy();
            
            while ($current->copy()->addMinutes($duration)->lessThanOrEqualTo($endTime)) {
                $slotStart = $current->copy();
                $slotEnd = $current->copy()->addMinutes($duration);
                
                // Verificar si el slot está disponible (no hay citas que se superpongan)
                $isAvailable = true;
                
                foreach ($appointments as $appointment) {
                    $appointmentStart = Carbon::parse($appointment->start_time);
                    $appointmentEnd = Carbon::parse($appointment->end_time);
                    
                    // Si hay superposición, el slot no está disponible
                    if ($slotStart->lessThan($appointmentEnd) && $slotEnd->greaterThan($appointmentStart)) {
                        $isAvailable = false;
                        break;
                    }
                }
                
                if ($isAvailable) {
                    $slots[] = [
                        'start' => $slotStart->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                    ];
                }
                
                // Avanzar al siguiente slot
                $current->addMinutes($duration);
            }
        }
        
        return $slots;
    }
    
    /**
     * Get all doctor availabilities for a specific date.
     *
     * @param int $doctorId
     * @param int $clinicId
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getDoctorAvailabilitiesForDate($doctorId, $clinicId, $date)
    {
        return DoctorAvailability::forClinic($clinicId)
            ->forDoctor($doctorId)
            ->active()
            ->where(function ($query) use ($date) {
                // Disponibilidades para la fecha específica
                $query->where('date', $date);
                
                // O disponibilidades recurrentes que incluyan la fecha
                $query->orWhere(function ($q) use ($date) {
                    $dateObj = Carbon::parse($date);
                    
                    // Recurrencia diaria
                    $q->where('recurrence', 'daily')
                      ->where('date', '<=', $date)
                      ->where(function ($q2) use ($date) {
                          $q2->whereNull('recurrence_end')
                             ->orWhere('recurrence_end', '>=', $date);
                      });
                });
                
                // Recurrencia semanal (mismo día de la semana)
                $query->orWhere(function ($q) use ($date) {
                    $dateObj = Carbon::parse($date);
                    $dayOfWeek = $dateObj->dayOfWeek;
                    
                    $q->where('recurrence', 'weekly')
                      ->whereRaw("DAYOFWEEK(date) = ?", [$dayOfWeek + 1])
                      ->where('date', '<=', $date)
                      ->where(function ($q2) use ($date) {
                          $q2->whereNull('recurrence_end')
                             ->orWhere('recurrence_end', '>=', $date);
                      });
                });
                
                // Recurrencia mensual (mismo día del mes)
                $query->orWhere(function ($q) use ($date) {
                    $dateObj = Carbon::parse($date);
                    $dayOfMonth = $dateObj->day;
                    
                    $q->where('recurrence', 'monthly')
                      ->whereRaw("DAY(date) = ?", [$dayOfMonth])
                      ->where('date', '<=', $date)
                      ->where(function ($q2) use ($date) {
                          $q2->whereNull('recurrence_end')
                             ->orWhere('recurrence_end', '>=', $date);
                      });
                });
            })
            ->get();
    }
    
    /**
     * Get all doctor appointments for a specific date.
     *
     * @param int $doctorId
     * @param int $clinicId
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getDoctorAppointmentsForDate($doctorId, $clinicId, $date)
    {
        return \Modules\Appointments\Models\Appointment::forClinic($clinicId)
            ->where('doctor_id', $doctorId)
            ->whereDate('start_time', $date)
            ->whereNotIn('status', ['cancelled'])
            ->get();
    }
}