<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Appointments\Models\AppointmentType;

class AppointmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegúrate de que clinic_id corresponda a una clínica existente en tu base de datos
        $clinicId = 1; // Cambia esto según tu estructura

        $appointmentTypes = [
            [
                'clinic_id' => $clinicId,
                'name' => 'Consulta General',
                'color' => '#4CAF50', // Verde NaturalBIO
                'description' => 'Consulta médica general para evaluación inicial',
                'default_price' => 200.00,
                'default_duration' => 30,
                'active' => true,
            ],
            [
                'clinic_id' => $clinicId,
                'name' => 'Masaje Terapéutico',
                'color' => '#00ACC1', // Azul Serenidad
                'description' => 'Sesión de masaje terapéutico para alivio del dolor',
                'default_price' => 250.00,
                'default_duration' => 60,
                'active' => true,
            ],
            [
                'clinic_id' => $clinicId,
                'name' => 'Terapia Integral',
                'color' => '#FBC02D', // Dorado Natural
                'description' => 'Terapia que combina varias técnicas para un tratamiento integral',
                'default_price' => 350.00,
                'default_duration' => 90,
                'active' => true,
            ],
            [
                'clinic_id' => $clinicId,
                'name' => 'Seguimiento',
                'color' => '#546E7A', // Gris Piedra
                'description' => 'Consulta de seguimiento para pacientes en tratamiento',
                'default_price' => 150.00,
                'default_duration' => 20,
                'active' => true,
            ],
            [
                'clinic_id' => $clinicId,
                'name' => 'Emergencia',
                'color' => '#F44336', // Rojo
                'description' => 'Atención de urgencia para casos que requieren intervención inmediata',
                'default_price' => 400.00,
                'default_duration' => 45,
                'active' => true,
            ],
        ];

        foreach ($appointmentTypes as $type) {
            AppointmentType::create($type);
        }
    }
}