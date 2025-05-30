<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Alta Verapaz', 'code' => 'AVE'],
            ['name' => 'Baja Verapaz', 'code' => 'BVE'],
            ['name' => 'Chimaltenango', 'code' => 'CHI'],
            ['name' => 'Chiquimula', 'code' => 'CHQ'],
            ['name' => 'El Progreso', 'code' => 'EPR'],
            ['name' => 'Escuintla', 'code' => 'ESC'],
            ['name' => 'Guatemala', 'code' => 'GUA'],
            ['name' => 'Huehuetenango', 'code' => 'HUE'],
            ['name' => 'Izabal', 'code' => 'IZA'],
            ['name' => 'Jalapa', 'code' => 'JAL'],
            ['name' => 'Jutiapa', 'code' => 'JUT'],
            ['name' => 'Petén', 'code' => 'PET'],
            ['name' => 'Quetzaltenango', 'code' => 'QUE'],
            ['name' => 'Quiché', 'code' => 'QUI'],
            ['name' => 'Retalhuleu', 'code' => 'RET'],
            ['name' => 'Sacatepéquez', 'code' => 'SAC'],
            ['name' => 'San Marcos', 'code' => 'SMA'],
            ['name' => 'Santa Rosa', 'code' => 'SRO'],
            ['name' => 'Sololá', 'code' => 'SOL'],
            ['name' => 'Suchitepéquez', 'code' => 'SUC'],
            ['name' => 'Totonicapán', 'code' => 'TOT'],
            ['name' => 'Zacapa', 'code' => 'ZAC'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}