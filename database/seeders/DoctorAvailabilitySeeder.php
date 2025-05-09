<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Appointments\Models\DoctorAvailability;
use Carbon\Carbon;

class DoctorAvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegúrate de que clinic_id corresponda a una clínica existente
        $clinicId = 1;
        
        // Asegúrate de que doctor_id corresponda a usuarios con rol de doctor
        // Aquí asumimos que ya tienes usuarios con roles creados
        $doctorIds = [2, 3]; // Cambiar según tus usuarios
        
        // Generar disponibilidades para la próxima semana
        $today = Carbon::now();
        $startOfWeek = $today->copy()->startOfWeek();
        
        foreach ($doctorIds as $doctorId) {
            // Disponibilidad para días laborables (lunes a viernes)
            for ($day = 0; $day < 5; $day++) {
                $date = $startOfWeek->copy()->addDays($day);
                
                // Mañana (8:00 - 12:00)
                DoctorAvailability::create([
                    'clinic_id' => $clinicId,
                    'doctor_id' => $doctorId,
                    'date' => $date->format('Y-m-d'),
                    'start_time' => '08:00',
                    'end_time' => '12:00',
                    'active' => true,
                ]);
                
                // Tarde (14:00 - 18:00)
                DoctorAvailability::create([
                    'clinic_id' => $clinicId,
                    'doctor_id' => $doctorId,
                    'date' => $date->format('Y-m-d'),
                    'start_time' => '14:00',
                    'end_time' => '18:00',
                    'active' => true,
                ]);
            }
            
            // Disponibilidad para sábado (solo mañana)
            $saturday = $startOfWeek->copy()->addDays(5);
            DoctorAvailability::create([
                'clinic_id' => $clinicId,
                'doctor_id' => $doctorId,
                'date' => $saturday->format('Y-m-d'),
                'start_time' => '08:00',
                'end_time' => '13:00',
                'active' => true,
            ]);
            
            // Agregar algunas recurrencias
            DoctorAvailability::create([
                'clinic_id' => $clinicId,
                'doctor_id' => $doctorId,
                'date' => $startOfWeek->format('Y-m-d'), // Lunes
                'start_time' => '08:00',
                'end_time' => '12:00',
                'recurrence' => 'weekly',
                'recurrence_end' => $startOfWeek->copy()->addMonths(3)->format('Y-m-d'),
                'active' => true,
            ]);
        }
    }
}