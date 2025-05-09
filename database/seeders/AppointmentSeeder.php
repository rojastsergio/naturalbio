<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Appointments\Models\Appointment;
use Modules\Appointments\Models\AppointmentType;
use Modules\Patients\Models\Patient;
use App\Models\User;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegúrate de que clinic_id corresponda a una clínica existente
        $clinicId = 1;
        
        // Obtener pacientes existentes
        $patients = Patient::where('clinic_id', $clinicId)->limit(10)->get();
        
        // Obtener doctores (usuarios con rol doctor)
        $doctors = User::role('doctor')->where('clinic_id', $clinicId)->get();
        
        // Obtener tipos de citas
        $appointmentTypes = AppointmentType::where('clinic_id', $clinicId)->get();
        
        // Si no hay suficientes datos, evitar errores
        if ($patients->isEmpty() || $doctors->isEmpty() || $appointmentTypes->isEmpty()) {
            $this->command->info('No hay suficientes datos para crear citas de ejemplo. Asegúrate de tener pacientes, doctores y tipos de citas.');
            return;
        }
        
        // Crear citas para esta semana y la próxima
        $today = Carbon::now();
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfNextWeek = $today->copy()->addWeeks(2)->endOfWeek();
        
        // Estados posibles para las citas
        $statuses = ['scheduled', 'completed', 'cancelled', 'no-show'];
        
        // Citas pasadas (última semana, todas completadas o canceladas)
        for ($i = 0; $i < 20; $i++) {
            $patient = $patients->random();
            $doctor = $doctors->random();
            $appointmentType = $appointmentTypes->random();
            
            $pastDate = $today->copy()->subDays(rand(1, 7));
            $startTime = $pastDate->copy()->setHour(rand(8, 17))->setMinute(0);
            $endTime = $startTime->copy()->addMinutes($appointmentType->default_duration);
            
            // Solo estados pasados (completada, cancelada, no asistió)
            $pastStatus = $statuses[rand(1, 3)];
            
            Appointment::create([
                'clinic_id' => $clinicId,
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_type_id' => $appointmentType->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $pastStatus,
                'total_price' => $appointmentType->default_price,
                'notes' => 'Cita de ejemplo creada mediante seeder.',
                'created_by' => $doctors->first()->id,
                'created_at' => $pastDate->copy()->subDays(rand(1, 10)),
                'updated_at' => $pastDate,
            ]);
        }
        
        // Citas futuras (próximos días, todas programadas)
        for ($day = 0; $day < 14; $day++) {
            $date = $today->copy()->addDays($day);
            
            // 2-4 citas por día
            $appointmentsPerDay = rand(2, 4);
            
            for ($i = 0; $i < $appointmentsPerDay; $i++) {
                $patient = $patients->random();
                $doctor = $doctors->random();
                $appointmentType = $appointmentTypes->random();
                
                $hour = rand(8, 17);
                $minute = rand(0, 1) * 30; // 0 o 30 minutos
                
                $startTime = $date->copy()->setHour($hour)->setMinute($minute);
                $endTime = $startTime->copy()->addMinutes($appointmentType->default_duration);
                
                // Si es del pasado, puede tener cualquier estado; si es futuro, solo programada
                $status = $date->isPast() ? $statuses[rand(0, 3)] : 'scheduled';
                
                // 10% de probabilidad de ser emergencia
                $emergency = (rand(1, 100) <= 10);
                
                Appointment::create([
                    'clinic_id' => $clinicId,
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'appointment_type_id' => $appointmentType->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => $status,
                    'total_price' => $appointmentType->default_price,
                    'notes' => $emergency ? 'EMERGENCIA: Requiere atención inmediata' : 'Cita de ejemplo creada mediante seeder.',
                    'emergency' => $emergency,
                    'created_by' => $doctors->first()->id,
                    'created_at' => $today->copy()->subDays(rand(1, 3)),
                    'updated_at' => $today,
                ]);
            }
        }
    }
}