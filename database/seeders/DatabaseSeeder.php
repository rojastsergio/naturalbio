<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentsTableSeeder::class,
            MunicipalitiesTableSeeder::class,
            ClinicsTableSeeder::class,
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            DoctorPermissionsSeeder::class,
            DoctorTableSeeder::class,
            DoctorAvailabilitySeeder::class,
            PatientsTableSeeder::class,
            AppointmentTypeSeeder::class,
            AppointmentSeeder::class,
            TherapyTableSeeder::class,
            TherapyInstructionTableSeeder::class,
            TherapyAssignmentTableSeeder::class,
            TherapyProgressTableSeeder::class,
            PrescriptionsTableSeeder::class,
            PrescriptionItemsTableSeeder::class,
            PatientRecommendationsSeeder::class,
            RecommendationPermissionsSeeder::class,
        ]);

        // Crear un usuario administrador usando firstOrCreate
        $user = User::firstOrCreate(
            ['email' => 'admin@naturalbio.com'],
            [
                'name' => 'Admin NaturalBIO',
                'password' => Hash::make('password'),
                'clinic_id' => 1,
            ]
        );
        
        // Asignar rol solo si es necesario (si el usuario acaba de ser creado o no tiene el rol)
        if (!$user->hasRole('owner')) {
            $user->assignRole('owner');
        }
    }
}