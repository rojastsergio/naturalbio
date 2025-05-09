<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Doctors\Models\Doctor;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Support\Facades\Hash;

class DoctorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarse de que existe al menos una clínica
        $clinic = Clinic::firstOrFail();
        
        // Crear usuarios doctores
        $doctors = [
            [
                'name' => 'Dr. Carlos Mendoza',
                'email' => 'carlos.mendoza@naturalbio.com',
                'password' => Hash::make('password'),
                'clinic_id' => $clinic->id,
                'specialty' => 'Medicina Natural',
                'license_number' => 'MN-12345',
                'biography' => 'Especialista en medicina natural con más de 15 años de experiencia en fitoterapia y tratamientos alternativos.',
                'consultation_fee' => 350.00,
                'accepts_emergencies' => true
            ],
            [
                'name' => 'Dra. María Fernández',
                'email' => 'maria.fernandez@naturalbio.com',
                'password' => Hash::make('password'),
                'clinic_id' => $clinic->id,
                'specialty' => 'Nutrición Holística',
                'license_number' => 'NH-54321',
                'biography' => 'Nutricionista especializada en alimentación basada en plantas y suplementación natural para tratamientos integrales.',
                'consultation_fee' => 300.00,
                'accepts_emergencies' => false
            ],
            [
                'name' => 'Dr. Roberto Estrada',
                'email' => 'roberto.estrada@naturalbio.com',
                'password' => Hash::make('password'),
                'clinic_id' => $clinic->id,
                'specialty' => 'Acupuntura',
                'license_number' => 'AC-78910',
                'biography' => 'Especialista en medicina tradicional china y acupuntura con formación en Beijing y más de 10 años de práctica clínica.',
                'consultation_fee' => 400.00,
                'accepts_emergencies' => true
            ],
            [
                'name' => 'Dra. Lucía González',
                'email' => 'lucia.gonzalez@naturalbio.com',
                'password' => Hash::make('password'),
                'clinic_id' => $clinic->id,
                'specialty' => 'Homeopatía',
                'license_number' => 'HM-24680',
                'biography' => 'Homeópata certificada con especialidad en tratamiento de enfermedades crónicas y autoinmunes mediante métodos naturales.',
                'consultation_fee' => 325.00,
                'accepts_emergencies' => false
            ],
            [
                'name' => 'Dr. Alejandro Paz',
                'email' => 'alejandro.paz@naturalbio.com',
                'password' => Hash::make('password'),
                'clinic_id' => $clinic->id,
                'specialty' => 'Quiropraxia',
                'license_number' => 'QP-13579',
                'biography' => 'Quiropráctico especializado en ajustes vertebrales, tratamiento del dolor de espalda y problemas posturales con enfoque holístico.',
                'consultation_fee' => 375.00,
                'accepts_emergencies' => true
            ]
        ];
        
        foreach ($doctors as $doctorData) {
            // Extraer datos para el usuario
            $userData = [
                'name' => $doctorData['name'],
                'email' => $doctorData['email'],
                'password' => $doctorData['password'],
                'clinic_id' => $doctorData['clinic_id'],
            ];
            
            // Crear o recuperar el usuario
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Asignar rol de doctor
            if (!$user->hasRole('doctor')) {
                $user->assignRole('doctor');
            }
            
            // Extraer datos para el doctor
            $doctorModelData = [
                'user_id' => $user->id,
                'clinic_id' => $doctorData['clinic_id'],
                'specialty' => $doctorData['specialty'],
                'license_number' => $doctorData['license_number'],
                'biography' => $doctorData['biography'],
                'consultation_fee' => $doctorData['consultation_fee'],
                'accepts_emergencies' => $doctorData['accepts_emergencies']
            ];
            
            // Crear o actualizar el doctor
            Doctor::updateOrCreate(
                ['user_id' => $user->id],
                $doctorModelData
            );
        }
    }
}