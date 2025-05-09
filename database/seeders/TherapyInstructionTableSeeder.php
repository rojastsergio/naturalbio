<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Doctors\Models\Doctor;
use Modules\Therapies\Models\Therapy;
use Modules\Doctors\Models\TherapyInstruction;

class TherapyInstructionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos doctores y terapias para asignar
        $doctors = Doctor::take(3)->get();
        if ($doctors->isEmpty()) {
            $this->command->info('No doctors found. Please run DoctorTableSeeder first.');
            return;
        }
        
        $therapies = Therapy::where('status', 'active')->get();
        if ($therapies->isEmpty()) {
            $this->command->info('No active therapies found. Please run TherapyTableSeeder first.');
            return;
        }
        
        $instructions = [
            [
                'title' => 'Protocolo de Masaje Terapéutico para Dolor Lumbar',
                'description' => 'Indicaciones para aplicación de masaje terapéutico en pacientes con dolor lumbar crónico.',
                'instructions' => json_encode([
                    'fase1' => 'Iniciar con calentamiento suave en toda la zona lumbar durante 5 minutos.',
                    'fase2' => 'Aplicar técnica de presión profunda en puntos gatillo identificados.',
                    'fase3' => 'Realizar estiramientos suaves de la musculatura paravertebral.',
                    'fase4' => 'Finalizar con maniobras de relajación y deslizamiento superficial.'
                ]),
                'body_area' => 'lumbar',
                'media' => json_encode(['images' => ['instructions/lumbar-points.jpg']]),
                'status' => 'active'
            ],
            [
                'title' => 'Protocolo de Acupuntura para Migraña',
                'description' => 'Puntos específicos para tratamiento de migraña y cefalea tensional mediante acupuntura.',
                'instructions' => json_encode([
                    'puntos_principales' => [
                        'GB20 - Fengchi', 
                        'LI4 - Hegu', 
                        'LR3 - Taichong', 
                        'GB8 - Shuaigu'
                    ],
                    'puntos_secundarios' => [
                        'TE5 - Waiguan',
                        'GB14 - Yangbai'
                    ],
                    'metodo' => 'Inserción perpendicular a 0.5-1.0 cun de profundidad con estimulación manual cada 10 minutos.'
                ]),
                'body_area' => 'cabeza',
                'media' => json_encode(['images' => ['instructions/acupuntura-migrana.jpg']]),
                'status' => 'active'
            ],
            [
                'title' => 'Protocolo de Reflexología para Problemas Digestivos',
                'description' => 'Puntos de reflexología podal para el tratamiento de problemas digestivos como gastritis, distensión y estreñimiento.',
                'instructions' => json_encode([
                    'secuencia' => [
                        'Estimular zona del plexo solar (centro del pie).',
                        'Trabajar puntos del estómago (bajo la almohadilla del pie).',
                        'Estimular puntos del intestino delgado y grueso (recorrido lateral y centro del pie).',
                        'Aplicar presión en puntos del hígado y vesícula (lateral derecho del pie).'
                    ],
                    'duracion' => 'Cada punto debe trabajarse durante 1-2 minutos con presión moderada.'
                ]),
                'body_area' => 'pies',
                'media' => json_encode(['images' => ['instructions/reflexologia-digestion.jpg']]),
                'status' => 'active'
            ],
            [
                'title' => 'Protocolo de Aromaterapia para Estrés y Ansiedad',
                'description' => 'Mezclas de aceites esenciales y métodos de aplicación para reducir niveles de estrés y ansiedad.',
                'instructions' => json_encode([
                    'mezcla_basica' => [
                        'Lavanda (4 gotas)',
                        'Bergamota (3 gotas)',
                        'Manzanilla romana (2 gotas)',
                        'Ylang ylang (1 gota)'
                    ],
                    'aplicacion' => 'Diluir en 30ml de aceite base (almendras dulces o jojoba) y aplicar en masaje ligero en sienes, nuca y hombros. También puede usarse en difusor durante 20 minutos.'
                ]),
                'body_area' => 'general',
                'media' => null,
                'status' => 'active'
            ],
            [
                'title' => 'Protocolo de Ventosas para Tensión en Trapecio',
                'description' => 'Indicaciones para aplicación de ventosas en pacientes con tensión y contracturas en el trapecio.',
                'instructions' => json_encode([
                    'preparacion' => 'Aplicar aceite base en la zona para facilitar el deslizamiento.',
                    'colocacion' => 'Colocar 4-6 ventosas de tamaño mediano en los puntos más tensos del trapecio superior y medio.',
                    'tiempo' => 'Mantener ventosas estáticas durante 5-10 minutos o realizar deslizamiento durante 3-5 minutos.',
                    'contraindicaciones' => 'No aplicar sobre lunares, heridas abiertas o en pacientes con trastornos de coagulación.'
                ]),
                'body_area' => 'hombros',
                'media' => json_encode(['images' => ['instructions/ventosas-trapecio.jpg']]),
                'status' => 'active'
            ]
        ];
        
        // Distribuir las instrucciones entre los doctores y terapias disponibles
        foreach ($instructions as $index => $instructionData) {
            // Asignar doctor y terapia cíclicamente
            $doctor = $doctors[$index % count($doctors)];
            $therapy = $therapies[$index % count($therapies)];
            
            TherapyInstruction::updateOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'title' => $instructionData['title']
                ],
                [
                    'therapy_id' => $therapy->id,
                    'description' => $instructionData['description'],
                    'instructions' => $instructionData['instructions'],
                    'body_area' => $instructionData['body_area'],
                    'media' => $instructionData['media'],
                    'status' => $instructionData['status']
                ]
            );
        }
    }
}