<?php

namespace Database\Seeders;

use App\Models\Municipality;
use App\Models\Department;
use Illuminate\Database\Seeder;

class MunicipalitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener los departamentos
        $guatemala = Department::where('name', 'Guatemala')->first();
        
        if ($guatemala) {
            // Municipios del departamento de Guatemala
            $municipalities = [
                ['name' => 'Ciudad de Guatemala', 'code' => 'GUA01', 'department_id' => $guatemala->id],
                ['name' => 'Santa Catarina Pinula', 'code' => 'GUA02', 'department_id' => $guatemala->id],
                ['name' => 'San José Pinula', 'code' => 'GUA03', 'department_id' => $guatemala->id],
                ['name' => 'San José del Golfo', 'code' => 'GUA04', 'department_id' => $guatemala->id],
                ['name' => 'Palencia', 'code' => 'GUA05', 'department_id' => $guatemala->id],
                ['name' => 'Chinautla', 'code' => 'GUA06', 'department_id' => $guatemala->id],
                ['name' => 'San Pedro Ayampuc', 'code' => 'GUA07', 'department_id' => $guatemala->id],
                ['name' => 'Mixco', 'code' => 'GUA08', 'department_id' => $guatemala->id],
                ['name' => 'San Pedro Sacatepéquez', 'code' => 'GUA09', 'department_id' => $guatemala->id],
                ['name' => 'San Juan Sacatepéquez', 'code' => 'GUA10', 'department_id' => $guatemala->id],
                ['name' => 'San Raymundo', 'code' => 'GUA11', 'department_id' => $guatemala->id],
                ['name' => 'Chuarrancho', 'code' => 'GUA12', 'department_id' => $guatemala->id],
                ['name' => 'Fraijanes', 'code' => 'GUA13', 'department_id' => $guatemala->id],
                ['name' => 'Amatitlán', 'code' => 'GUA14', 'department_id' => $guatemala->id],
                ['name' => 'Villa Nueva', 'code' => 'GUA15', 'department_id' => $guatemala->id],
                ['name' => 'Villa Canales', 'code' => 'GUA16', 'department_id' => $guatemala->id],
                ['name' => 'Petapa', 'code' => 'GUA17', 'department_id' => $guatemala->id],
            ];

            foreach ($municipalities as $municipality) {
                Municipality::create($municipality);
            }
        }
    }
}