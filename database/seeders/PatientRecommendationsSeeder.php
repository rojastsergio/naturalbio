<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Recommendations\Models\PatientRecommendation;
use Modules\Patients\Models\Patient;
use Modules\Supplements\Models\Supplement;
use Modules\Therapies\Models\Therapy;
use App\Models\User;
use Carbon\Carbon;

class PatientRecommendationsSeeder extends Seeder
{
    /**
     * Ejecutar el seeder de datos para las recomendaciones.
     */
    public function run(): void
    {
        // Obtenemos pacientes, usuarios, suplementos y terapias existentes para referenciarlos
        $patients = Patient::all();
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['doctor', 'therapist']);
        })->get();
        $supplements = Supplement::where('status', 'active')->get();
        $therapies = Therapy::where('status', 'active')->get();

        // Si no hay datos suficientes, no continuamos
        if ($patients->isEmpty() || $users->isEmpty()) {
            $this->command->info('No hay pacientes o usuarios suficientes para crear recomendaciones de ejemplo.');
            return;
        }

        // Creamos datos para recomendaciones de suplementos
        if ($supplements->isNotEmpty()) {
            foreach ($patients->take(5) as $patient) {
                $supplement = $supplements->random();
                $user = $users->random();
                
                PatientRecommendation::create([
                    'patient_id' => $patient->id,
                    'created_by' => $user->id,
                    'title' => "Tomar suplemento: {$supplement->name}",
                    'type' => 'supplement',
                    'reference_id' => $supplement->id,
                    'tasks' => [
                        [
                            'title' => "Tomar {$supplement->name} diariamente",
                            'description' => $supplement->instructions ?? 'Seguir las instrucciones del envase',
                            'completed' => rand(0, 1) == 1
                        ],
                        [
                            'title' => "Reportar efectos después de 7 días",
                            'description' => 'Informar cualquier cambio o mejora observada',
                            'completed' => false
                        ]
                    ],
                    'accepted' => rand(0, 3) > 0, // 75% de probabilidad de ser aceptada
                    'due_date' => Carbon::now()->addDays(rand(10, 30))->toDateString(),
                    'progress' => rand(0, 80), // Progreso aleatorio
                    'notes' => 'Recomendación basada en necesidades nutricionales del paciente',
                    'status' => 'active',
                ]);
            }
        }

        // Creamos datos para recomendaciones de terapias
        if ($therapies->isNotEmpty()) {
            foreach ($patients->take(5) as $patient) {
                $therapy = $therapies->random();
                $user = $users->random();
                
                PatientRecommendation::create([
                    'patient_id' => $patient->id,
                    'created_by' => $user->id,
                    'title' => "Asistir a terapia: {$therapy->name}",
                    'type' => 'therapy',
                    'reference_id' => $therapy->id,
                    'tasks' => [
                        [
                            'title' => "Acudir a sesión de {$therapy->name}",
                            'description' => $therapy->description ?? 'Realizar la terapia completa',
                            'completed' => rand(0, 1) == 1
                        ],
                        [
                            'title' => "Realizar ejercicios en casa",
                            'description' => 'Seguir las indicaciones del terapeuta',
                            'completed' => false
                        ]
                    ],
                    'accepted' => rand(0, 3) > 0, // 75% de probabilidad de ser aceptada
                    'due_date' => Carbon::now()->addDays(rand(5, 20))->toDateString(),
                    'progress' => rand(0, 100), // Progreso aleatorio
                    'notes' => 'Recomendada para mejorar condición física y bienestar',
                    'status' => rand(0, 5) > 0 ? 'active' : 'completed', // 83% activas, 17% completadas
                ]);
            }
        }

        // Creamos algunas recomendaciones personalizadas
        foreach ($patients->take(3) as $patient) {
            $user = $users->random();
            
            PatientRecommendation::create([
                'patient_id' => $patient->id,
                'created_by' => $user->id,
                'title' => "Plan de cuidado personalizado",
                'type' => 'custom',
                'tasks' => [
                    [
                        'title' => "Descansar adecuadamente",
                        'description' => 'Dormir al menos 8 horas diarias',
                        'completed' => rand(0, 1) == 1
                    ],
                    [
                        'title' => "Hidratación constante",
                        'description' => 'Tomar 2 litros de agua al día',
                        'completed' => rand(0, 1) == 1
                    ],
                    [
                        'title' => "Reducir consumo de azúcares",
                        'description' => 'Evitar bebidas azucaradas y postres procesados',
                        'completed' => rand(0, 1) == 1
                    ]
                ],
                'accepted' => true,
                'due_date' => Carbon::now()->addDays(45)->toDateString(),
                'progress' => rand(30, 90), // Progreso aleatorio más avanzado
                'notes' => 'Plan personalizado basado en historial médico y necesidades específicas',
                'status' => 'active',
            ]);
        }

        // Recomendaciones canceladas para tener variedad de estados
        foreach ($patients->take(2) as $patient) {
            $user = $users->random();
            
            PatientRecommendation::create([
                'patient_id' => $patient->id,
                'created_by' => $user->id,
                'title' => "Plan cancelado o sustituido",
                'type' => 'custom',
                'tasks' => [
                    [
                        'title' => "Tarea que ya no aplica",
                        'description' => 'Esta recomendación fue reemplazada por otra más adecuada',
                        'completed' => false
                    ]
                ],
                'accepted' => false,
                'due_date' => Carbon::now()->addDays(10)->toDateString(),
                'progress' => 0,
                'notes' => 'Cancelada por cambio en el tratamiento',
                'status' => 'cancelled',
            ]);
        }

        $this->command->info('Se han creado recomendaciones de ejemplo para los pacientes.');
    }
}