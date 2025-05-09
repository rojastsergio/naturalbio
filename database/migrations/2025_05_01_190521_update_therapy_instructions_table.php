<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo ejecutar esta migración si la tabla ya existe
        // Si es una instalación nueva, esta migración no hará nada
        if (Schema::hasTable('therapy_instructions')) {
            // Verificamos si hay claves foráneas que necesitamos eliminar
            $hasDoctorFK = $this->hasForeignKey('therapy_instructions', 'doctor_id');
            $hasClinicFK = $this->hasForeignKey('therapy_instructions', 'clinic_id');
            
            // Si la tabla tiene columnas antiguas que necesitan actualizarse
            $hasOldStructure = Schema::hasColumn('therapy_instructions', 'name') || 
                               Schema::hasColumn('therapy_instructions', 'body_areas') ||
                               !Schema::hasColumn('therapy_instructions', 'status');
            
            // Solo ejecutamos la migración si es necesario
            if ($hasDoctorFK || $hasClinicFK || $hasOldStructure) {
                // Desactivar verificación de claves foráneas temporalmente
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                
                Schema::table('therapy_instructions', function (Blueprint $table) {
                    // Eliminamos las restricciones de clave externa solo si existen
                    if ($this->hasForeignKey('therapy_instructions', 'doctor_id')) {
                        $table->dropForeign(['doctor_id']);
                    }
                    
                    if ($this->hasForeignKey('therapy_instructions', 'clinic_id')) {
                        $table->dropForeign(['clinic_id']);
                    }
                    
                    // Agregar therapy_id si no existe
                    if (!Schema::hasColumn('therapy_instructions', 'therapy_id')) {
                        $table->foreignId('therapy_id')->after('id')->nullable()->constrained()->onDelete('cascade');
                    }
                    
                    // Modificar doctor_id para que sea constrained con onDelete cascade
                    if (Schema::hasColumn('therapy_instructions', 'doctor_id')) {
                        $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');
                    }
                    
                    // Eliminar clinic_id solo si existe
                    if (Schema::hasColumn('therapy_instructions', 'clinic_id')) {
                        $table->dropColumn('clinic_id');
                    }
                    
                    // Renombrar name a title si existe
                    if (Schema::hasColumn('therapy_instructions', 'name') && 
                        !Schema::hasColumn('therapy_instructions', 'title')) {
                        $table->renameColumn('name', 'title');
                    }
                    
                    // Modificar instructions a JSON si existe y no es del tipo correcto
                    if (Schema::hasColumn('therapy_instructions', 'instructions')) {
                        // Verificar si la columna no es ya JSON
                        $columnType = DB::connection()->getDoctrineColumn('therapy_instructions', 'instructions')->getType()->getName();
                        if ($columnType != 'json') {
                            $table->json('instructions')->nullable()->change();
                        }
                    }
                    
                    // Manejar body_areas/body_area
                    if (Schema::hasColumn('therapy_instructions', 'body_areas') && 
                        !Schema::hasColumn('therapy_instructions', 'body_area')) {
                        // Primero eliminar la columna body_areas
                        $table->dropColumn('body_areas');
                        // Luego crear la nueva columna body_area
                        $table->string('body_area')->nullable()->after('description');
                    }
                    
                    // Agregar status si no existe
                    if (!Schema::hasColumn('therapy_instructions', 'status')) {
                        $table->string('status')->default('active')->after('media');
                    }
                });
                
                // Reactivar verificación de claves foráneas
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            }
        }
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // La reversión solo se ejecuta si la tabla existe
        if (Schema::hasTable('therapy_instructions')) {
            Schema::table('therapy_instructions', function (Blueprint $table) {
                // Solo revertimos si estamos en la estructura nueva
                if (Schema::hasColumn('therapy_instructions', 'therapy_id')) {
                    $table->dropForeign(['therapy_id']);
                    $table->dropColumn('therapy_id');
                }
                
                if (Schema::hasColumn('therapy_instructions', 'title') && 
                    !Schema::hasColumn('therapy_instructions', 'name')) {
                    $table->renameColumn('title', 'name');
                }
                
                if (!Schema::hasColumn('therapy_instructions', 'clinic_id')) {
                    $table->foreignId('clinic_id')->nullable();
                }
                
                if (Schema::hasColumn('therapy_instructions', 'body_area') &&
                    !Schema::hasColumn('therapy_instructions', 'body_areas')) {
                    $table->dropColumn('body_area');
                    $table->json('body_areas')->nullable();
                }
                
                if (Schema::hasColumn('therapy_instructions', 'status')) {
                    $table->dropColumn('status');
                }
                
                // Restaurar las claves foráneas originales
                if (Schema::hasColumn('therapy_instructions', 'doctor_id') &&
                    !$this->hasForeignKey('therapy_instructions', 'doctor_id')) {
                    $table->foreign('doctor_id')->references('id')->on('doctors');
                }
            });
        }
    }
    
    /**
     * Verifica si una columna tiene una clave foránea
     */
    private function hasForeignKey($table, $column)
    {
        try {
            $conn = Schema::getConnection();
            $prefix = $conn->getTablePrefix();
            $foreignKeys = $conn->getDoctrineSchemaManager()->listTableForeignKeys($prefix.$table);
            
            // Buscar si alguna clave foránea utiliza la columna especificada
            foreach ($foreignKeys as $foreignKey) {
                if (in_array($column, $foreignKey->getLocalColumns())) {
                    return true;
                }
            }
            
            return false;
        } catch (\Exception $e) {
            // Si hay un error al verificar, asumimos que no existe
            return false;
        }
    }
};