<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Municipality;
use Illuminate\Database\Seeder;

class ClinicsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el municipio de Ciudad de Guatemala
        $municipality = Municipality::where('name', 'Ciudad de Guatemala')->first();
        
        if ($municipality) {
            Clinic::create([
                'name' => 'NaturalBIO Sede Central',
                'address' => 'Zona 10, Ciudad de Guatemala',
                'phone' => '+502 2222-3333',
                'email' => 'info@naturalbio.com',
                'status' => true,
                'municipality_id' => $municipality->id,
            ]);
            
            Clinic::create([
                'name' => 'NaturalBIO Zona 16',
                'address' => 'Zona 16, Ciudad de Guatemala',
                'phone' => '+502 2222-4444',
                'email' => 'zona16@naturalbio.com',
                'status' => true,
                'municipality_id' => $municipality->id,
            ]);
        }
    }
}