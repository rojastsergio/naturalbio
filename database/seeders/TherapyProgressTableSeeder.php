<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Therapies\Models\TherapyAssignment;
use Modules\Therapies\Models\TherapyProgress;
use Carbon\Carbon;

class TherapyProgressTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener asignaciones de terapias con estado completado o en progreso
        $assignments = TherapyAssignment::whereIn('status', ['completed', 'in_progress'])->get();
        if ($assignments->isEmpty()) {
            $this->command->info('No completed or in-progress therapy assignments found. Please run TherapyAssignmentTableSeeder first.');
            return;
        }
        
        foreach ($assignments as $assignment) {
            // Obtener la fecha de la cita
            $appointmentDate = Carbon::parse($assignment->appointment->scheduled_at);
            
            // Calcular el progreso y status basado en el estado de la asignación
            $isCompleted = $assignment->status === 'completed';
            $maxProgress = $isCompleted ? 100 : rand(10, 90);
            
            // Número de registros de progreso (1-3)
            $progressEntries = $isCompleted ? rand(2, 3) : 1;
            
            for ($i = 0; $i < $progressEntries; $i++) {
                // Calcular progreso para esta entrada
                // Si es la última entrada de una terapia completada, será 100%
                $progress = ($i == $progressEntries - 1 && $isCompleted) 
                    ? 100 
                    : rand(
                        max(10, (int)($maxProgress * $i / $progressEntries)), 
                        min($maxProgress, (int)($maxProgress * ($i + 1) / $progressEntries))
                    );
                
                // Estado basado en el progreso
                $status = 'not_started';
                if ($progress == 100) {
                    $status = 'completed';
                } elseif ($progress > 0) {
                    $status = 'in_progress';
                }
                
                // Fecha de registro (aleatoria entre la fecha de la cita y hoy, o justo la fecha de la cita si es en el futuro)
                $recordedAt = $appointmentDate->isFuture() 
                    ? $appointmentDate 
                    : Carbon::parse($appointmentDate)->addMinutes(rand(0, 60 * 24 * 3)); // Hasta 3 días después
                
                // Evitar fechas futuras
                if ($recordedAt->isFuture()) {
                    $recordedAt = Carbon::now();
                }
                
                // Crear notas basadas en el progreso
                $notes = null;
                if ($progress > 0) {
                    $notes = match(true) {
                        $progress < 30 => "Iniciado el tratamiento. El paciente muestra ligera mejoría inicial.",
                        $progress < 60 => "Progreso moderado. Se observa reducción de síntomas y mejor movilidad.",
                        $progress < 100 => "Avance significativo. El paciente reporta gran mejoría en su condición.",
                        default => "Tratamiento completado con éxito. El paciente ha alcanzado los objetivos terapéuticos."
                    };
                }
                
                // Crear el registro de progreso
                TherapyProgress::create([
                    'therapy_assignment_id' => $assignment->id,
                    'recorded_by' => $assignment->therapist_id,
                    'recorded_at' => $recordedAt,
                    'progress_percentage' => $progress,
                    'status' => $status,
                    'notes' => $notes
                ]);
            }
        }
    }
}