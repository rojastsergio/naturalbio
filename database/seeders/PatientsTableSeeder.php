<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class PatientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        $clinicId = DB::table('clinics')->first()->id;
        $municipalityIds = DB::table('municipalities')->pluck('id')->toArray();

        // Crear 30 pacientes de ejemplo
        for ($i = 0; $i < 30; $i++) {
            $patientId = DB::table('patients')->insertGetId([
                'clinic_id' => $clinicId,
                'expedient_number' => 'EXP-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'name' => $faker->firstName,
                'last_name' => $faker->lastName . ' ' . $faker->lastName,
                'email' => $faker->optional(0.7)->safeEmail,
                'phone' => $faker->optional(0.9)->numerify('####-####'),
                'whatsapp' => $faker->optional(0.8)->numerify('+502 ####-####'),
                'birthdate' => $faker->dateTimeBetween('-80 years', '-5 years')->format('Y-m-d'),
                'gender' => $faker->randomElement(['Masculino', 'Femenino']),
                'address' => $faker->optional(0.8)->address,
                'municipality_id' => $faker->randomElement($municipalityIds),
                'medical_history' => $faker->optional(0.6)->paragraph,
                'status' => $faker->randomElement(['active', 'active', 'active', 'inactive']),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => now(),
            ]);

            // Crear entre 1 y 5 registros de signos vitales para cada paciente
            $vitalSignsCount = rand(1, 5);
            for ($j = 0; $j < $vitalSignsCount; $j++) {
                $recordedAt = $faker->dateTimeBetween('-6 months', 'now');
                $weight = $faker->randomFloat(1, 40, 110);
                $height = $faker->randomFloat(1, 140, 190);
                $bmi = $height > 0 ? round($weight / (($height/100) * ($height/100)), 1) : null;

                DB::table('vital_signs')->insert([
                    'patient_id' => $patientId,
                    'recorded_at' => $recordedAt,
                    'blood_pressure' => $faker->optional(0.9)->numerify('##0/##'),
                    'oxygen_saturation' => $faker->optional(0.9)->randomFloat(1, 90, 100),
                    'blood_glucose' => $faker->optional(0.7)->randomFloat(1, 70, 180),
                    'heart_rate' => $faker->optional(0.9)->numberBetween(60, 100),
                    'respiratory_rate' => $faker->optional(0.8)->numberBetween(12, 20),
                    'height' => $height,
                    'weight' => $weight,
                    'bmi' => $bmi,
                    'notes' => $faker->optional(0.5)->sentence,
                    'recorded_by' => null, // Puedes asignar un usuario enfermera si existe
                    'created_at' => $recordedAt,
                    'updated_at' => $recordedAt,
                ]);
            }
        }
    }
}