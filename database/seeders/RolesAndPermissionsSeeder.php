<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos por módulo
        $modules = [
            'patients',
            'appointments',
            'doctors',
            'therapies',
            'prescriptions',
            'supplements',
            'vitals', // Añadimos módulo para signos vitales
            'recommendations',
            'billing',
            'inventory',
            'reports',
            'settings',
            'therapy_assignments', // Añadimos módulo para asignaciones de terapia
            'therapy_progress', // Añadimos módulo para progreso de terapia
        ];

        $actions = ['create', 'view', 'update', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action} {$module}", 'guard_name' => 'web']);
            }
        }

        // Permisos adicionales específicos
        Permission::firstOrCreate(['name' => 'manage modules', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage roles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage forms', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'send reminders', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage availabilities', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage therapy_instructions', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view own data', 'guard_name' => 'web']);

        // Crear roles y asignar permisos
        $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $ownerRole->syncPermissions(Permission::all());

        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorRole->syncPermissions([
            'create patients', 'view patients', 'update patients',
            'create appointments', 'view appointments', 'update appointments',
            'create therapies', 'view therapies', 'update therapies',
            'create prescriptions', 'view prescriptions', 'update prescriptions',
            'create supplements', 'view supplements', 'update supplements',
            'create vitals', 'view vitals', 'update vitals',
        ]);

        $receptionistRole = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $receptionistRole->syncPermissions([
            'view patients', 'create patients',
            'view appointments', 'create appointments',
            'send reminders',
        ]);

        $nurseRole = Role::firstOrCreate(['name' => 'nurse', 'guard_name' => 'web']);
        $nurseRole->syncPermissions([
            'view patients',
            'create vitals', 'update vitals', 'view vitals',
        ]);

        $therapistRole = Role::firstOrCreate(['name' => 'therapist', 'guard_name' => 'web']);
        $therapistRole->syncPermissions([
            'view therapy_assignments',
            'create therapy_progress', 'update therapy_progress', 'view therapy_progress',
        ]);

        $doctorRole = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
        $doctorRole->syncPermissions([
            'view patients',
            'view appointments', 'update appointments',
            'create prescriptions', 'update prescriptions', 'view prescriptions',
            'manage availabilities',
            'manage therapy_instructions',
            'view vitals',
        ]);

        $patientRole = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
        $patientRole->syncPermissions([
            'view own data',
            'update recommendations',
        ]);
    }
}