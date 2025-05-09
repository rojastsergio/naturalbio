<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrescriptionItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtenemos las recetas existentes
        $prescriptionIds = DB::table('prescriptions')->pluck('id')->toArray();

        // Si no hay recetas, no podemos crear ítems
        if (empty($prescriptionIds)) {
            $this->command->info('No hay recetas disponibles para crear ítems de ejemplo.');
            return;
        }

        // Array para almacenar los ítems de receta
        $items = [];
        $now = Carbon::now();

        // Para cada receta, creamos entre 1 y 4 ítems
        foreach ($prescriptionIds as $prescriptionId) {
            $itemCount = rand(1, 4);

            for ($i = 0; $i < $itemCount; $i++) {
                // Determinamos aleatoriamente si es un suplemento
                $isSupplement = rand(0, 3) === 0; // 1/4 chance
                
                // Si es un suplemento y hay una tabla supplements, podríamos relacionarlo
                // Esto es opcional y depende de si tienes esa tabla ya implementada
                $supplementId = null;
                /*
                if ($isSupplement) {
                    $supplementIds = DB::table('supplements')->pluck('id')->toArray();
                    if (!empty($supplementIds)) {
                        $supplementId = $supplementIds[array_rand($supplementIds)];
                    }
                }
                */

                // Obtener datos de medicamento o suplemento
                list($name, $posology, $instructions) = $this->getMedicationData($isSupplement);

                $items[] = [
                    'prescription_id' => $prescriptionId,
                    'name' => $name,
                    'posology' => $posology,
                    'instructions' => $instructions,
                    'is_supplement' => $isSupplement,
                    'supplement_id' => $supplementId,
                    'dosage' => $this->getDosageExample(),
                    'frequency' => $this->getFrequencyExample(),
                    'duration' => $this->getDurationExample(),
                    'notes' => rand(0, 1) ? $this->getNotesExample() : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('prescription_items')->insert($items);
    }

    /**
     * Get medication or supplement data.
     */
    private function getMedicationData(bool $isSupplement): array
    {
        if ($isSupplement) {
            $supplements = [
                [
                    'Omega 3',
                    '1 cápsula de 1000mg',
                    'Tomar con alimentos, preferiblemente en la mañana.'
                ],
                [
                    'Vitamina D3',
                    '1 cápsula de 2000 UI',
                    'Tomar con el desayuno para mejorar la absorción.'
                ],
                [
                    'Magnesio',
                    '1 tableta de 300mg',
                    'Tomar por la noche para mejorar la calidad del sueño.'
                ],
                [
                    'Probiótico',
                    '1 cápsula de 50 billones de UFC',
                    'Tomar en ayunas con un vaso de agua.'
                ],
                [
                    'Complejo B',
                    '1 tableta',
                    'Tomar después del desayuno para evitar náuseas.'
                ],
                [
                    'Colágeno hidrolizado',
                    '1 cucharada (10g) disuelta en agua',
                    'Tomar preferiblemente en ayunas.'
                ],
            ];
            return $supplements[array_rand($supplements)];
        } else {
            $medications = [
                [
                    'Paracetamol 500mg',
                    '1-2 tabletas cada 6-8 horas según necesidad',
                    'No exceder 4 gramos en 24 horas. Puede tomarse con o sin alimentos.'
                ],
                [
                    'Ibuprofeno 400mg',
                    '1 tableta cada 8 horas',
                    'Tomar siempre con alimentos para reducir irritación gástrica.'
                ],
                [
                    'Loratadina 10mg',
                    '1 tableta diaria',
                    'Tomar preferiblemente por la mañana. Puede causar somnolencia.'
                ],
                [
                    'Omeprazol 20mg',
                    '1 cápsula antes del desayuno',
                    'Tomar en ayunas, 30 minutos antes del desayuno.'
                ],
                [
                    'Amoxicilina 500mg',
                    '1 cápsula cada 8 horas por 7 días',
                    'Completar el tratamiento aunque los síntomas mejoren. Tomar con o sin alimentos.'
                ],
                [
                    'Ciprofloxacino 500mg',
                    '1 tableta cada 12 horas por 5 días',
                    'Tomar con un vaso grande de agua. Evitar antiácidos y productos lácteos.'
                ],
                [
                    'Losartán 50mg',
                    '1 tableta cada 24 horas',
                    'Tomar a la misma hora todos los días, preferiblemente por la mañana.'
                ],
                [
                    'Metformina 850mg',
                    '1 tableta con el desayuno y la cena',
                    'Tomar con alimentos para reducir efectos secundarios gastrointestinales.'
                ],
                [
                    'Atorvastatina 20mg',
                    '1 tableta al acostarse',
                    'Tomar preferiblemente por la noche. Evitar consumo de toronja.'
                ],
                [
                    'Sertralina 50mg',
                    '1 tableta cada mañana',
                    'Tomar con alimentos. Puede tardar 2-4 semanas en mostrar efectos completos.'
                ],
            ];
            return $medications[array_rand($medications)];
        }
    }

    /**
     * Get a random dosage example.
     */
    private function getDosageExample(): string
    {
        $dosages = [
            '1 tableta',
            '2 tabletas',
            '1 cápsula',
            '5 ml (1 cucharadita)',
            '10 ml (2 cucharaditas)',
            '15 ml (1 cucharada)',
            '1 ampolla',
            '1 sobre',
            '2 inhalaciones',
            '1 parche',
        ];

        return $dosages[array_rand($dosages)];
    }

    /**
     * Get a random frequency example.
     */
    private function getFrequencyExample(): string
    {
        $frequencies = [
            'Cada 8 horas',
            'Cada 12 horas',
            'Cada 24 horas',
            'Una vez al día',
            'Dos veces al día',
            'Tres veces al día',
            'Con el desayuno y la cena',
            'Antes de cada comida',
            'Al acostarse',
            'Según necesidad, máximo cada 6 horas',
        ];

        return $frequencies[array_rand($frequencies)];
    }

    /**
     * Get a random duration example.
     */
    private function getDurationExample(): string
    {
        $durations = [
            'Por 7 días',
            'Por 10 días',
            'Por 14 días',
            'Por 1 mes',
            'Por 3 meses',
            'De forma continua',
            'Hasta próxima consulta',
            'Durante el episodio agudo',
            'Por 5 días, luego evaluar',
            'Uso permanente',
        ];

        return $durations[array_rand($durations)];
    }

    /**
     * Get random notes example.
     */
    private function getNotesExample(): string
    {
        $notes = [
            'Suspender si aparece erupción cutánea.',
            'Monitorear presión arterial durante el tratamiento.',
            'Puede causar fotosensibilidad, evitar exposición solar.',
            'Evaluar respuesta en 2 semanas.',
            'No suspender abruptamente.',
            'Tener precaución al conducir o manejar maquinaria.',
            'Conservar en refrigeración.',
            'Agitar bien antes de usar.',
            'Puede teñir la orina de color naranja, no es motivo de preocupación.',
            'Interacción con alcohol puede causar somnolencia excesiva.',
        ];

        return $notes[array_rand($notes)];
    }
}