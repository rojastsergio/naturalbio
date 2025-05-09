<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Therapies\Models\Therapy;
use App\Models\Clinic;
use App\Models\User;

class TherapyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurarse de que existe al menos una clínica y un usuario administrador
        $clinic = Clinic::firstOrFail();
        $admin = User::where('email', 'admin@naturalbio.com')->first();
        
        $therapies = [
            [
                'name' => 'Masaje Terapéutico',
                'description' => 'Técnica manual de manipulación de tejidos blandos para aliviar la tensión muscular, mejorar la circulación y promover la relajación.',
                'default_price' => 250.00,
                'price' => 250.00,
                'default_duration' => 60,
                'media' => json_encode(['image' => 'therapies/masaje-terapeutico.jpg']),
                'status' => 'active'
            ],
            [
                'name' => 'Acupuntura',
                'description' => 'Técnica de medicina tradicional china que consiste en la inserción de agujas finas en puntos específicos del cuerpo para equilibrar la energía y aliviar dolencias.',
                'default_price' => 350.00,
                'price' => 350.00,
                'default_duration' => 45,
                'media' => json_encode(['image' => 'therapies/acupuntura.jpg']),
                'status' => 'active'
            ],
            [
                'name' => 'Terapia con Ventosas',
                'description' => 'Técnica que utiliza copas de succión para aumentar el flujo sanguíneo local, aliviar la tensión muscular y promover la curación natural.',
                'default_price' => 200.00,
                'price' => 220.00,
                'default_duration' => 30,
                'media' => json_encode(['image' => 'therapies/ventosas.jpg']),
                'status' => 'active'
            ],
            [
                'name' => 'Reflexología',
                'description' => 'Técnica de masaje en pies y manos que trabaja sobre puntos reflejos conectados a órganos y sistemas del cuerpo para promover el equilibrio y la salud.',
                'default_price' => 180.00,
                'price' => 180.00,
                'default_duration' => 40,
                'media' => json_encode(['image' => 'therapies/reflexologia.jpg']),
                'status' => 'active'
            ],
            [
                'name' => 'Terapia de Piedras Calientes',
                'description' => 'Tratamiento que utiliza piedras volcánicas calientes colocadas estratégicamente en el cuerpo para relajar músculos tensos y mejorar la circulación.',
                'default_price' => 300.00,
                'price' => 320.00,
                'default_duration' => 75,
                'media' => json_encode(['image' => 'therapies/piedras-calientes.jpg']),
                'status' => 'active'
            ],
            [
                'name' => 'Aromaterapia',
                'description' => 'Uso terapéutico de aceites esenciales extraídos de plantas para mejorar el bienestar físico, mental y emocional.',
                'default_price' => 220.00,
                'price' => 220.00,
                'default_duration' => 50,
                'media' => json_encode(['image' => 'therapies/aromaterapia.jpg']),
                'status' => 'active'
            ],
            [
                'name' => 'Sauna Infrarrojo',
                'description' => 'Tratamiento que utiliza luz infrarroja para calentar directamente el cuerpo, ayudando a eliminar toxinas, aliviar dolores musculares y mejorar la circulación.',
                'default_price' => 150.00,
                'price' => 150.00,
                'default_duration' => 30,
                'media' => json_encode(['image' => 'therapies/sauna-infrarrojo.jpg']),
                'status' => 'active'
            ],
            [
                'name' => 'Terapia de Polaridad',
                'description' => 'Sistema holístico que trabaja con el campo energético del cuerpo mediante toques, estiramientos y comunicación verbal para restablecer el equilibrio natural.',
                'default_price' => 280.00,
                'price' => 280.00,
                'default_duration' => 60,
                'media' => null,
                'status' => 'inactive'
            ]
        ];
        
        foreach ($therapies as $therapyData) {
            Therapy::updateOrCreate(
                [
                    'clinic_id' => $clinic->id,
                    'name' => $therapyData['name']
                ],
                [
                    'description' => $therapyData['description'],
                    'default_price' => $therapyData['default_price'],
                    'price' => $therapyData['price'],
                    'default_duration' => $therapyData['default_duration'],
                    'media' => $therapyData['media'],
                    'status' => $therapyData['status'],
                    'created_by' => $admin ? $admin->id : null
                ]
            );
        }
    }
}