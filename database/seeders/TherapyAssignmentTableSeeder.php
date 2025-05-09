<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Appointments\Models\Appointment;
use Modules\Therapies\Models\Therapy;
use Modules\Therapies\Models\TherapyAssignment;
use App\Models\User;
use Carbon\Carbon;

class TherapyAssignmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener citas, terapias y terapistas
        $appointments = Appointment::take(10)->get();
        if ($appointments->isEmpty()) {
            $this->command->info('No appointments found. Please run AppointmentSeeder first.');
            return;
        }
        
        $therapies = Therapy::where('status', 'active')->get();
        if ($therapies->isEmpty()) {
            $this->command->info('No active therapies found. Please run TherapyTableSeeder first.');
            return;
        }
        
        $therapists = User::role('therapist')->get();
        if ($therapists->isEmpty()) {
            // Si no hay terapeutas, usar un admin como fallback
            $therapists = User::role('owner')->get();
            if ($therapists->isEmpty()) {
                $this->command->info('No therapists or admins found. Please run UserSeeder first.');
                return;
            }
        }
        
        // Estatus posibles
        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        
        // Para cada cita, asignar 1-2 terapias aleatoriamente
        foreach ($appointments as $appointment) {
            // Número aleatorio de terapias para esta cita (1 o 2)
            $numTherapies = rand(1, min(2, count($therapies)));
            
            // Seleccionar terapias aleatorias
            $selectedTherapies = $therapies->random($numTherapies);
            
            foreach ($selectedTherapies as $therapy) {
                // Seleccionar un terapista aleatorio
                $therapist = $therapists->random();
                
                // Determinar un estado basado en la fecha de la cita
                $status = 'pending';
                $today = Carbon::now();
                $appointmentDate = Carbon::parse($appointment->scheduled_at);
                
                if ($appointmentDate->lt($today)) {
                    // Si la cita ya pasó, asignar un estado aleatorio entre completado y cancelado
                    $status = $statuses[rand(2, 3)];
                } elseif ($appointmentDate->isToday()) {
                    // Si la cita es hoy, podría estar en progreso
                    $status = $statuses[rand(0, 2)];
                }
                
                // Crear la asignación
                TherapyAssignment::updateOrCreate(
                    [
                        'appointment_id' => $appointment->id,
                        'therapy_id' => $therapy->id
                    ],
                    [
                        'therapist_id' => $therapist->id,
                        'price' => $therapy->price,
                        'duration' => $therapy->default_duration,
                        'status' => $status,
                        'notes' => "Asignación de {$therapy->name} para el paciente en la cita del " . $appointmentDate->format('d/m/Y H:i'),
                        'assigned_by' => $appointment->created_by
                    ]
                );
            }
        }
    }
}