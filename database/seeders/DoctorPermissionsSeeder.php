<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DoctorPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            'view doctors',
            'create doctors',
            'update doctors',
            'delete doctors',
            'manage availabilities',
            'manage therapy_instructions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar permisos al rol de doctor
        $doctorRole = Role::firstOrCreate(['name' => 'doctor']);
        $doctorRole->givePermissionTo(['view doctors', 'manage availabilities', 'manage therapy_instructions']);

        // Asignar permisos al rol de propietario (owner)
        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $ownerRole->givePermissionTo($permissions);

        // Asignar permisos al rol de editor
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->givePermissionTo(['view doctors', 'create doctors', 'update doctors']);
    }
}