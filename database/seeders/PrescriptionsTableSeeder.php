<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Prescriptions\Models\Prescription;

class PrescriptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenemos IDs existentes para asegurarnos de que las relaciones son válidas
        $clinicIds = DB::table('clinics')->pluck('id')->toArray();
        $patientIds = DB::table('patients')->pluck('id')->toArray();
        $doctorIds = DB::table('doctors')->pluck('id')->toArray();

        // Si no hay datos, no podemos crear recetas
        if (empty($clinicIds) || empty($patientIds) || empty($doctorIds)) {
            $this->command->info('No hay clínicas, pacientes o doctores disponibles para crear recetas de ejemplo.');
            return;
        }

        // Creamos 10 recetas de ejemplo
        $prescriptions = [];
        $now = Carbon::now();

        for ($i = 1; $i <= 10; $i++) {
            $prescriptions[] = [
                'clinic_id' => $clinicIds[array_rand($clinicIds)],
                'patient_id' => $patientIds[array_rand($patientIds)],
                'doctor_id' => $doctorIds[array_rand($doctorIds)],
                'prescription_number' => 'RX-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'issue_date' => $now->subDays(rand(0, 30))->format('Y-m-d'),
                'expiry_date' => $now->addDays(rand(30, 90))->format('Y-m-d'),
                'diagnosis' => $this->getDiagnosisExample(),
                'instructions' => $this->getInstructionsExample(),
                'status' => rand(0, 5) > 0 ? 'active' : 'inactive', // 5/6 chance of active
                'notification_sent' => rand(0, 1),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('prescriptions')->insert($prescriptions);
    }

    /**
     * Get a random diagnosis for example.
     */
    private function getDiagnosisExample(): string
    {
        $diagnoses = [
            'Hipertensión arterial grado I',
            'Migraña sin aura',
            'Dermatitis atópica leve',
            'Gastritis crónica',
            'Ansiedad generalizada',
            'Hipotiroidismo subclínico',
            'Artrosis de rodilla',
            'Asma bronquial controlada',
            'Síndrome de intestino irritable',
            'Rinitis alérgica estacional',
        ];

        return $diagnoses[array_rand($diagnoses)];
    }

    /**
     * Get random instructions for example.
     */
    private function getInstructionsExample(): string
    {
        $instructions = [
            'Mantener hidratación adecuada. Evitar alimentos irritantes.',
            'Reposo relativo. Continuar actividad física moderada.',
            'Aplicar compresas frías en caso de dolor agudo. Evitar exposición al sol.',
            'Seguir dieta baja en grasas. Evitar bebidas alcohólicas y cafeína.',
            'Mantener horarios regulares de sueño. Practicar técnicas de relajación.',
            'Realizar ejercicios respiratorios 3 veces al día. Evitar ambientes con humo.',
            'Control de glucemia diario. Seguir plan alimentario recomendado.',
            'Comenzar actividad física gradual. Vigilar presión arterial.',
            'Aplicar crema hidratante después del baño. Usar jabón neutro.',
            'Tener medicación de rescate siempre disponible. Control en un mes.',
        ];

        return $instructions[array_rand($instructions)];
    }
}