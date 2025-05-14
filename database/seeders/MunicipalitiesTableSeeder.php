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
        // Crear array asociativo para almacenar los departamentos
        $departments = [];
        
        // Cargar todos los departamentos
        $departmentRecords = Department::all();
        foreach ($departmentRecords as $department) {
            $departments[$department->name] = $department->id;
        }

        // Verificar si hay departamentos
        if (empty($departments)) {
            $this->command->error('No hay departamentos en la base de datos. Ejecuta DepartmentsTableSeeder primero.');
            return;
        }

        // Array con todos los municipios de Guatemala organizado por departamento
        $municipalitiesByDepartment = [
            // Alta Verapaz
            'Alta Verapaz' => [
                ['name' => 'Cobán', 'code' => 'AVE01'],
                ['name' => 'Santa Cruz Verapaz', 'code' => 'AVE02'],
                ['name' => 'San Cristóbal Verapaz', 'code' => 'AVE03'],
                ['name' => 'Tactic', 'code' => 'AVE04'],
                ['name' => 'Tamahú', 'code' => 'AVE05'],
                ['name' => 'Tucurú', 'code' => 'AVE06'],
                ['name' => 'Panzós', 'code' => 'AVE07'],
                ['name' => 'Senahú', 'code' => 'AVE08'],
                ['name' => 'San Pedro Carchá', 'code' => 'AVE09'],
                ['name' => 'San Juan Chamelco', 'code' => 'AVE10'],
                ['name' => 'Lanquín', 'code' => 'AVE11'],
                ['name' => 'Cahabón', 'code' => 'AVE12'],
                ['name' => 'Chisec', 'code' => 'AVE13'],
                ['name' => 'Chahal', 'code' => 'AVE14'],
                ['name' => 'Fray Bartolomé de las Casas', 'code' => 'AVE15'],
                ['name' => 'Santa Catalina La Tinta', 'code' => 'AVE16'],
                ['name' => 'Raxruhá', 'code' => 'AVE17'],
            ],
            
            // Baja Verapaz
            'Baja Verapaz' => [
                ['name' => 'Salamá', 'code' => 'BVE01'],
                ['name' => 'San Miguel Chicaj', 'code' => 'BVE02'],
                ['name' => 'Rabinal', 'code' => 'BVE03'],
                ['name' => 'Cubulco', 'code' => 'BVE04'],
                ['name' => 'Granados', 'code' => 'BVE05'],
                ['name' => 'El Chol', 'code' => 'BVE06'],
                ['name' => 'San Jerónimo', 'code' => 'BVE07'],
                ['name' => 'Purulhá', 'code' => 'BVE08'],
            ],
            
            // Chimaltenango
            'Chimaltenango' => [
                ['name' => 'Chimaltenango', 'code' => 'CHI01'],
                ['name' => 'San José Poaquil', 'code' => 'CHI02'],
                ['name' => 'San Martín Jilotepeque', 'code' => 'CHI03'],
                ['name' => 'Comalapa', 'code' => 'CHI04'],
                ['name' => 'Santa Apolonia', 'code' => 'CHI05'],
                ['name' => 'Tecpán Guatemala', 'code' => 'CHI06'],
                ['name' => 'Patzún', 'code' => 'CHI07'],
                ['name' => 'Pochuta', 'code' => 'CHI08'],
                ['name' => 'Patzicía', 'code' => 'CHI09'],
                ['name' => 'Santa Cruz Balanyá', 'code' => 'CHI10'],
                ['name' => 'Acatenango', 'code' => 'CHI11'],
                ['name' => 'Yepocapa', 'code' => 'CHI12'],
                ['name' => 'San Andrés Itzapa', 'code' => 'CHI13'],
                ['name' => 'Parramos', 'code' => 'CHI14'],
                ['name' => 'Zaragoza', 'code' => 'CHI15'],
                ['name' => 'El Tejar', 'code' => 'CHI16'],
            ],
            
            // Chiquimula
            'Chiquimula' => [
                ['name' => 'Chiquimula', 'code' => 'CHQ01'],
                ['name' => 'San José La Arada', 'code' => 'CHQ02'],
                ['name' => 'San Juan Ermita', 'code' => 'CHQ03'],
                ['name' => 'Jocotán', 'code' => 'CHQ04'],
                ['name' => 'Camotán', 'code' => 'CHQ05'],
                ['name' => 'Olopa', 'code' => 'CHQ06'],
                ['name' => 'Esquipulas', 'code' => 'CHQ07'],
                ['name' => 'Concepción Las Minas', 'code' => 'CHQ08'],
                ['name' => 'Quezaltepeque', 'code' => 'CHQ09'],
                ['name' => 'San Jacinto', 'code' => 'CHQ10'],
                ['name' => 'Ipala', 'code' => 'CHQ11'],
            ],
            
            // El Progreso
            'El Progreso' => [
                ['name' => 'Guastatoya', 'code' => 'EPR01'],
                ['name' => 'Morazán', 'code' => 'EPR02'],
                ['name' => 'San Agustín Acasaguastlán', 'code' => 'EPR03'],
                ['name' => 'San Cristóbal Acasaguastlán', 'code' => 'EPR04'],
                ['name' => 'El Jícaro', 'code' => 'EPR05'],
                ['name' => 'Sansare', 'code' => 'EPR06'],
                ['name' => 'Sanarate', 'code' => 'EPR07'],
                ['name' => 'San Antonio La Paz', 'code' => 'EPR08'],
            ],
            
            // Escuintla
            'Escuintla' => [
                ['name' => 'Escuintla', 'code' => 'ESC01'],
                ['name' => 'Santa Lucía Cotzumalguapa', 'code' => 'ESC02'],
                ['name' => 'La Democracia', 'code' => 'ESC03'],
                ['name' => 'Siquinalá', 'code' => 'ESC04'],
                ['name' => 'Masagua', 'code' => 'ESC05'],
                ['name' => 'Tiquisate', 'code' => 'ESC06'],
                ['name' => 'La Gomera', 'code' => 'ESC07'],
                ['name' => 'Guanagazapa', 'code' => 'ESC08'],
                ['name' => 'San José', 'code' => 'ESC09'],
                ['name' => 'Iztapa', 'code' => 'ESC10'],
                ['name' => 'Palín', 'code' => 'ESC11'],
                ['name' => 'San Vicente Pacaya', 'code' => 'ESC12'],
                ['name' => 'Nueva Concepción', 'code' => 'ESC13'],
                ['name' => 'Sipacate', 'code' => 'ESC14'],
            ],
            
            // Guatemala
            'Guatemala' => [
                ['name' => 'Ciudad de Guatemala', 'code' => 'GUA01'],
                ['name' => 'Santa Catarina Pinula', 'code' => 'GUA02'],
                ['name' => 'San José Pinula', 'code' => 'GUA03'],
                ['name' => 'San José del Golfo', 'code' => 'GUA04'],
                ['name' => 'Palencia', 'code' => 'GUA05'],
                ['name' => 'Chinautla', 'code' => 'GUA06'],
                ['name' => 'San Pedro Ayampuc', 'code' => 'GUA07'],
                ['name' => 'Mixco', 'code' => 'GUA08'],
                ['name' => 'San Pedro Sacatepéquez', 'code' => 'GUA09'],
                ['name' => 'San Juan Sacatepéquez', 'code' => 'GUA10'],
                ['name' => 'San Raymundo', 'code' => 'GUA11'],
                ['name' => 'Chuarrancho', 'code' => 'GUA12'],
                ['name' => 'Fraijanes', 'code' => 'GUA13'],
                ['name' => 'Amatitlán', 'code' => 'GUA14'],
                ['name' => 'Villa Nueva', 'code' => 'GUA15'],
                ['name' => 'Villa Canales', 'code' => 'GUA16'],
                ['name' => 'Petapa', 'code' => 'GUA17'],
            ],
            
            // Huehuetenango
            'Huehuetenango' => [
                ['name' => 'Huehuetenango', 'code' => 'HUE01'],
                ['name' => 'Chiantla', 'code' => 'HUE02'],
                ['name' => 'Malacatancito', 'code' => 'HUE03'],
                ['name' => 'Cuilco', 'code' => 'HUE04'],
                ['name' => 'Nentón', 'code' => 'HUE05'],
                ['name' => 'San Pedro Necta', 'code' => 'HUE06'],
                ['name' => 'Jacaltenango', 'code' => 'HUE07'],
                ['name' => 'Soloma', 'code' => 'HUE08'],
                ['name' => 'San Ildefonso Ixtahuacán', 'code' => 'HUE09'],
                ['name' => 'Santa Bárbara', 'code' => 'HUE10'],
                ['name' => 'La Libertad', 'code' => 'HUE11'],
                ['name' => 'La Democracia', 'code' => 'HUE12'],
                ['name' => 'San Miguel Acatán', 'code' => 'HUE13'],
                ['name' => 'San Rafael La Independencia', 'code' => 'HUE14'],
                ['name' => 'Todos Santos Cuchumatán', 'code' => 'HUE15'],
                ['name' => 'San Juan Atitán', 'code' => 'HUE16'],
                ['name' => 'Santa Eulalia', 'code' => 'HUE17'],
                ['name' => 'San Mateo Ixtatán', 'code' => 'HUE18'],
                ['name' => 'Colotenango', 'code' => 'HUE19'],
                ['name' => 'San Sebastián Huehuetenango', 'code' => 'HUE20'],
                ['name' => 'Tectitán', 'code' => 'HUE21'],
                ['name' => 'Concepción Huista', 'code' => 'HUE22'],
                ['name' => 'San Juan Ixcoy', 'code' => 'HUE23'],
                ['name' => 'San Antonio Huista', 'code' => 'HUE24'],
                ['name' => 'San Sebastián Coatán', 'code' => 'HUE25'],
                ['name' => 'Barillas', 'code' => 'HUE26'],
                ['name' => 'Aguacatán', 'code' => 'HUE27'],
                ['name' => 'San Rafael Petzal', 'code' => 'HUE28'],
                ['name' => 'San Gaspar Ixchil', 'code' => 'HUE29'],
                ['name' => 'Santiago Chimaltenango', 'code' => 'HUE30'],
                ['name' => 'Santa Ana Huista', 'code' => 'HUE31'],
                ['name' => 'Unión Cantinil', 'code' => 'HUE32'],
                ['name' => 'Petatán', 'code' => 'HUE33'],
            ],
            
            // Izabal
            'Izabal' => [
                ['name' => 'Puerto Barrios', 'code' => 'IZA01'],
                ['name' => 'Livingston', 'code' => 'IZA02'],
                ['name' => 'El Estor', 'code' => 'IZA03'],
                ['name' => 'Morales', 'code' => 'IZA04'],
                ['name' => 'Los Amates', 'code' => 'IZA05'],
            ],
            
            // Jalapa
            'Jalapa' => [
                ['name' => 'Jalapa', 'code' => 'JAL01'],
                ['name' => 'San Pedro Pinula', 'code' => 'JAL02'],
                ['name' => 'San Luis Jilotepeque', 'code' => 'JAL03'],
                ['name' => 'San Manuel Chaparrón', 'code' => 'JAL04'],
                ['name' => 'San Carlos Alzatate', 'code' => 'JAL05'],
                ['name' => 'Monjas', 'code' => 'JAL06'],
                ['name' => 'Mataquescuintla', 'code' => 'JAL07'],
            ],
            
            // Jutiapa
            'Jutiapa' => [
                ['name' => 'Jutiapa', 'code' => 'JUT01'],
                ['name' => 'El Progreso', 'code' => 'JUT02'],
                ['name' => 'Santa Catarina Mita', 'code' => 'JUT03'],
                ['name' => 'Agua Blanca', 'code' => 'JUT04'],
                ['name' => 'Asunción Mita', 'code' => 'JUT05'],
                ['name' => 'Yupiltepeque', 'code' => 'JUT06'],
                ['name' => 'Atescatempa', 'code' => 'JUT07'],
                ['name' => 'Jerez', 'code' => 'JUT08'],
                ['name' => 'El Adelanto', 'code' => 'JUT09'],
                ['name' => 'Zapotitlán', 'code' => 'JUT10'],
                ['name' => 'Comapa', 'code' => 'JUT11'],
                ['name' => 'Jalpatagua', 'code' => 'JUT12'],
                ['name' => 'Conguaco', 'code' => 'JUT13'],
                ['name' => 'Moyuta', 'code' => 'JUT14'],
                ['name' => 'Pasaco', 'code' => 'JUT15'],
                ['name' => 'San José Acatempa', 'code' => 'JUT16'],
                ['name' => 'Quesada', 'code' => 'JUT17'],
            ],
            
            // Petén
            'Petén' => [
                ['name' => 'Flores', 'code' => 'PET01'],
                ['name' => 'San José', 'code' => 'PET02'],
                ['name' => 'San Benito', 'code' => 'PET03'],
                ['name' => 'San Andrés', 'code' => 'PET04'],
                ['name' => 'La Libertad', 'code' => 'PET05'],
                ['name' => 'San Francisco', 'code' => 'PET06'],
                ['name' => 'Santa Ana', 'code' => 'PET07'],
                ['name' => 'Dolores', 'code' => 'PET08'],
                ['name' => 'San Luis', 'code' => 'PET09'],
                ['name' => 'Sayaxché', 'code' => 'PET10'],
                ['name' => 'Melchor de Mencos', 'code' => 'PET11'],
                ['name' => 'Poptún', 'code' => 'PET12'],
                ['name' => 'Las Cruces', 'code' => 'PET13'],
                ['name' => 'El Chal', 'code' => 'PET14'],
            ],
            
            // Quetzaltenango
            'Quetzaltenango' => [
                ['name' => 'Quetzaltenango', 'code' => 'QUE01'],
                ['name' => 'Salcajá', 'code' => 'QUE02'],
                ['name' => 'Olintepeque', 'code' => 'QUE03'],
                ['name' => 'San Carlos Sija', 'code' => 'QUE04'],
                ['name' => 'Sibilia', 'code' => 'QUE05'],
                ['name' => 'Cabricán', 'code' => 'QUE06'],
                ['name' => 'Cajolá', 'code' => 'QUE07'],
                ['name' => 'San Miguel Siguilá', 'code' => 'QUE08'],
                ['name' => 'San Juan Ostuncalco', 'code' => 'QUE09'],
                ['name' => 'San Mateo', 'code' => 'QUE10'],
                ['name' => 'Concepción Chiquirichapa', 'code' => 'QUE11'],
                ['name' => 'San Martín Sacatepéquez', 'code' => 'QUE12'],
                ['name' => 'Almolonga', 'code' => 'QUE13'],
                ['name' => 'Cantel', 'code' => 'QUE14'],
                ['name' => 'Huitán', 'code' => 'QUE15'],
                ['name' => 'Zunil', 'code' => 'QUE16'],
                ['name' => 'Colomba', 'code' => 'QUE17'],
                ['name' => 'San Francisco La Unión', 'code' => 'QUE18'],
                ['name' => 'El Palmar', 'code' => 'QUE19'],
                ['name' => 'Coatepeque', 'code' => 'QUE20'],
                ['name' => 'Génova', 'code' => 'QUE21'],
                ['name' => 'Flores Costa Cuca', 'code' => 'QUE22'],
                ['name' => 'La Esperanza', 'code' => 'QUE23'],
                ['name' => 'Palestina de Los Altos', 'code' => 'QUE24'],
            ],
            
            // Quiché
            'Quiché' => [
                ['name' => 'Santa Cruz del Quiché', 'code' => 'QUI01'],
                ['name' => 'Chiché', 'code' => 'QUI02'],
                ['name' => 'Chinique', 'code' => 'QUI03'],
                ['name' => 'Zacualpa', 'code' => 'QUI04'],
                ['name' => 'Chajul', 'code' => 'QUI05'],
                ['name' => 'Chichicastenango', 'code' => 'QUI06'],
                ['name' => 'Patzité', 'code' => 'QUI07'],
                ['name' => 'San Antonio Ilotenango', 'code' => 'QUI08'],
                ['name' => 'San Pedro Jocopilas', 'code' => 'QUI09'],
                ['name' => 'Cunén', 'code' => 'QUI10'],
                ['name' => 'San Juan Cotzal', 'code' => 'QUI11'],
                ['name' => 'Joyabaj', 'code' => 'QUI12'],
                ['name' => 'Nebaj', 'code' => 'QUI13'],
                ['name' => 'San Andrés Sajcabajá', 'code' => 'QUI14'],
                ['name' => 'Uspantán', 'code' => 'QUI15'],
                ['name' => 'Sacapulas', 'code' => 'QUI16'],
                ['name' => 'San Bartolomé Jocotenango', 'code' => 'QUI17'],
                ['name' => 'Canillá', 'code' => 'QUI18'],
                ['name' => 'Chicamán', 'code' => 'QUI19'],
                ['name' => 'Ixcán', 'code' => 'QUI20'],
                ['name' => 'Pachalum', 'code' => 'QUI21'],
            ],
            
            // Retalhuleu
            'Retalhuleu' => [
                ['name' => 'Retalhuleu', 'code' => 'RET01'],
                ['name' => 'San Sebastián', 'code' => 'RET02'],
                ['name' => 'Santa Cruz Muluá', 'code' => 'RET03'],
                ['name' => 'San Martín Zapotitlán', 'code' => 'RET04'],
                ['name' => 'San Felipe', 'code' => 'RET05'],
                ['name' => 'San Andrés Villa Seca', 'code' => 'RET06'],
                ['name' => 'Champerico', 'code' => 'RET07'],
                ['name' => 'Nuevo San Carlos', 'code' => 'RET08'],
                ['name' => 'El Asintal', 'code' => 'RET09'],
            ],
            
            // Sacatepéquez
            'Sacatepéquez' => [
                ['name' => 'Antigua Guatemala', 'code' => 'SAC01'],
                ['name' => 'Jocotenango', 'code' => 'SAC02'],
                ['name' => 'Pastores', 'code' => 'SAC03'],
                ['name' => 'Sumpango', 'code' => 'SAC04'],
                ['name' => 'Santo Domingo Xenacoj', 'code' => 'SAC05'],
                ['name' => 'Santiago Sacatepéquez', 'code' => 'SAC06'],
                ['name' => 'San Bartolomé Milpas Altas', 'code' => 'SAC07'],
                ['name' => 'San Lucas Sacatepéquez', 'code' => 'SAC08'],
                ['name' => 'Santa Lucía Milpas Altas', 'code' => 'SAC09'],
                ['name' => 'Magdalena Milpas Altas', 'code' => 'SAC10'],
                ['name' => 'Santa María de Jesús', 'code' => 'SAC11'],
                ['name' => 'Ciudad Vieja', 'code' => 'SAC12'],
                ['name' => 'San Miguel Dueñas', 'code' => 'SAC13'],
                ['name' => 'Alotenango', 'code' => 'SAC14'],
                ['name' => 'San Antonio Aguas Calientes', 'code' => 'SAC15'],
                ['name' => 'Santa Catarina Barahona', 'code' => 'SAC16'],
            ],
            
            // San Marcos
            'San Marcos' => [
                ['name' => 'San Marcos', 'code' => 'SMA01'],
                ['name' => 'San Pedro Sacatepéquez', 'code' => 'SMA02'],
                ['name' => 'San Antonio Sacatepéquez', 'code' => 'SMA03'],
                ['name' => 'Comitancillo', 'code' => 'SMA04'],
                ['name' => 'San Miguel Ixtahuacán', 'code' => 'SMA05'],
                ['name' => 'Concepción Tutuapa', 'code' => 'SMA06'],
                ['name' => 'Tacaná', 'code' => 'SMA07'],
                ['name' => 'Sibinal', 'code' => 'SMA08'],
                ['name' => 'Tajumulco', 'code' => 'SMA09'],
                ['name' => 'Tejutla', 'code' => 'SMA10'],
                ['name' => 'San Rafael Pie de la Cuesta', 'code' => 'SMA11'],
                ['name' => 'Nuevo Progreso', 'code' => 'SMA12'],
                ['name' => 'El Tumbador', 'code' => 'SMA13'],
                ['name' => 'El Rodeo', 'code' => 'SMA14'],
                ['name' => 'Malacatán', 'code' => 'SMA15'],
                ['name' => 'Catarina', 'code' => 'SMA16'],
                ['name' => 'Ayutla (Tecún Umán)', 'code' => 'SMA17'],
                ['name' => 'Ocós', 'code' => 'SMA18'],
                ['name' => 'San Pablo', 'code' => 'SMA19'],
                ['name' => 'El Quetzal', 'code' => 'SMA20'],
                ['name' => 'La Reforma', 'code' => 'SMA21'],
                ['name' => 'Pajapita', 'code' => 'SMA22'],
                ['name' => 'Ixchiguán', 'code' => 'SMA23'],
                ['name' => 'San José Ojetenam', 'code' => 'SMA24'],
                ['name' => 'San Cristóbal Cucho', 'code' => 'SMA25'],
                ['name' => 'Sipacapa', 'code' => 'SMA26'],
                ['name' => 'Esquipulas Palo Gordo', 'code' => 'SMA27'],
                ['name' => 'Río Blanco', 'code' => 'SMA28'],
                ['name' => 'San Lorenzo', 'code' => 'SMA29'],
                ['name' => 'La Blanca', 'code' => 'SMA30'],
            ],
            
            // Santa Rosa
            'Santa Rosa' => [
                ['name' => 'Cuilapa', 'code' => 'SRO01'],
                ['name' => 'Barberena', 'code' => 'SRO02'],
                ['name' => 'Santa Rosa de Lima', 'code' => 'SRO03'],
                ['name' => 'Casillas', 'code' => 'SRO04'],
                ['name' => 'San Rafael Las Flores', 'code' => 'SRO05'],
                ['name' => 'Oratorio', 'code' => 'SRO06'],
                ['name' => 'San Juan Tecuaco', 'code' => 'SRO07'],
                ['name' => 'Chiquimulilla', 'code' => 'SRO08'],
                ['name' => 'Taxisco', 'code' => 'SRO09'],
                ['name' => 'Santa María Ixhuatán', 'code' => 'SRO10'],
                ['name' => 'Guazacapán', 'code' => 'SRO11'],
                ['name' => 'Santa Cruz Naranjo', 'code' => 'SRO12'],
                ['name' => 'Pueblo Nuevo Viñas', 'code' => 'SRO13'],
                ['name' => 'Nueva Santa Rosa', 'code' => 'SRO14'],
            ],
            
            // Sololá
            'Sololá' => [
                ['name' => 'Sololá', 'code' => 'SOL01'],
                ['name' => 'San José Chacayá', 'code' => 'SOL02'],
                ['name' => 'Santa María Visitación', 'code' => 'SOL03'],
                ['name' => 'Santa Lucía Utatlán', 'code' => 'SOL04'],
                ['name' => 'Nahualá', 'code' => 'SOL05'],
                ['name' => 'Santa Catarina Ixtahuacán', 'code' => 'SOL06'],
                ['name' => 'Santa Clara La Laguna', 'code' => 'SOL07'],
                ['name' => 'Concepción', 'code' => 'SOL08'],
                ['name' => 'San Andrés Semetabaj', 'code' => 'SOL09'],
                ['name' => 'Panajachel', 'code' => 'SOL10'],
                ['name' => 'Santa Catarina Palopó', 'code' => 'SOL11'],
                ['name' => 'San Antonio Palopó', 'code' => 'SOL12'],
                ['name' => 'San Lucas Tolimán', 'code' => 'SOL13'],
                ['name' => 'Santa Cruz La Laguna', 'code' => 'SOL14'],
                ['name' => 'San Pablo La Laguna', 'code' => 'SOL15'],
                ['name' => 'San Marcos La Laguna', 'code' => 'SOL16'],
                ['name' => 'San Juan La Laguna', 'code' => 'SOL17'],
                ['name' => 'San Pedro La Laguna', 'code' => 'SOL18'],
                ['name' => 'Santiago Atitlán', 'code' => 'SOL19'],
            ],
            
            // Suchitepéquez
            'Suchitepéquez' => [
                ['name' => 'Mazatenango', 'code' => 'SUC01'],
                ['name' => 'Cuyotenango', 'code' => 'SUC02'],
                ['name' => 'San Francisco Zapotitlán', 'code' => 'SUC03'],
                ['name' => 'San Bernardino', 'code' => 'SUC04'],
                ['name' => 'San José El Ídolo', 'code' => 'SUC05'],
                ['name' => 'Santo Domingo Suchitepéquez', 'code' => 'SUC06'],
                ['name' => 'San Lorenzo', 'code' => 'SUC07'],
                ['name' => 'Samayac', 'code' => 'SUC08'],
                ['name' => 'San Pablo Jocopilas', 'code' => 'SUC09'],
                ['name' => 'San Antonio Suchitepéquez', 'code' => 'SUC10'],
                ['name' => 'San Miguel Panán', 'code' => 'SUC11'],
                ['name' => 'San Gabriel', 'code' => 'SUC12'],
                ['name' => 'Chicacao', 'code' => 'SUC13'],
                ['name' => 'Patulul', 'code' => 'SUC14'],
                ['name' => 'Santa Bárbara', 'code' => 'SUC15'],
                ['name' => 'San Juan Bautista', 'code' => 'SUC16'],
                ['name' => 'Santo Tomás La Unión', 'code' => 'SUC17'],
                ['name' => 'Zunilito', 'code' => 'SUC18'],
                ['name' => 'Pueblo Nuevo', 'code' => 'SUC19'],
                ['name' => 'Río Bravo', 'code' => 'SUC20'],
                ['name' => 'San José La Máquina', 'code' => 'SUC21'],
            ],
            
            // Totonicapán
            'Totonicapán' => [
                ['name' => 'Totonicapán', 'code' => 'TOT01'],
                ['name' => 'San Cristóbal Totonicapán', 'code' => 'TOT02'],
                ['name' => 'San Francisco El Alto', 'code' => 'TOT03'],
                ['name' => 'San Andrés Xecul', 'code' => 'TOT04'],
                ['name' => 'Momostenango', 'code' => 'TOT05'],
                ['name' => 'Santa María Chiquimula', 'code' => 'TOT06'],
                ['name' => 'Santa Lucía La Reforma', 'code' => 'TOT07'],
                ['name' => 'San Bartolo', 'code' => 'TOT08'],
            ],
            
            // Zacapa
            'Zacapa' => [
                ['name' => 'Zacapa', 'code' => 'ZAC01'],
                ['name' => 'Estanzuela', 'code' => 'ZAC02'],
                ['name' => 'Río Hondo', 'code' => 'ZAC03'],
                ['name' => 'Gualán', 'code' => 'ZAC04'],
                ['name' => 'Teculután', 'code' => 'ZAC05'],
                ['name' => 'Usumatlán', 'code' => 'ZAC06'],
                ['name' => 'Cabañas', 'code' => 'ZAC07'],
                ['name' => 'San Diego', 'code' => 'ZAC08'],
                ['name' => 'La Unión', 'code' => 'ZAC09'],
                ['name' => 'Huité', 'code' => 'ZAC10'],
            ],
        ];

        // Crear contador para municipios nuevos y ya existentes
        $newCount = 0;
        $existingCount = 0;

        // Crear los municipios para cada departamento
        foreach ($municipalitiesByDepartment as $departmentName => $municipalities) {
            // Verificar si el departamento existe
            if (!isset($departments[$departmentName])) {
                $this->command->error("Departamento no encontrado: $departmentName");
                continue;
            }

            $departmentId = $departments[$departmentName];
            
            // Crear los municipios
            foreach ($municipalities as $municipalityData) {
                // Añadir el ID del departamento
                $municipalityData['department_id'] = $departmentId;
                
                // Verificar si el municipio ya existe
                $exists = Municipality::where('name', $municipalityData['name'])
                    ->where('department_id', $departmentId)
                    ->exists();
                
                if (!$exists) {
                    // Crear solo si no existe
                    Municipality::create($municipalityData);
                    $newCount++;
                } else {
                    $existingCount++;
                }
            }
        }
        
        $this->command->info("Proceso completado: Se agregaron $newCount nuevos municipios y se omitieron $existingCount municipios existentes.");
    }
}