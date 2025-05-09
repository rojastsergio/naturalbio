<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios terapeutas
        $therapistRole = Role::where('name', 'therapist')->first();
        $adminRole = Role::where('name', 'admin')->first();
        
        // Crear algunos terapeutas
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => 'Terapeuta ' . $i,
                'email' => 'therapist' . $i . '@naturalbio.com',
                'password' => Hash::make('password'),
                'clinic_id' => 1,
            ]);
            
            if ($therapistRole) {
                $user->assignRole($therapistRole);
            }
        }
        
        // Crear un administrador adicional
        $admin = User::create([
            'name' => 'Admin Tres',
            'email' => 'admin3@naturalbio.com',
            'password' => Hash::make('password'),
            'clinic_id' => 1,
        ]);
        
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }
    }
}