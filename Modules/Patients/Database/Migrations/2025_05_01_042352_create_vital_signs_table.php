<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->date('recorded_at');
            $table->string('blood_pressure')->nullable(); // PA
            $table->float('oxygen_saturation')->nullable(); // SpO₂
            $table->float('blood_glucose')->nullable(); // Glucosa
            $table->integer('heart_rate')->nullable(); // FC
            $table->integer('respiratory_rate')->nullable(); // FR
            $table->float('height')->nullable(); // Estatura en cm
            $table->float('weight')->nullable(); // Peso en kg
            $table->float('bmi')->nullable(); // IMC (calculado)
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};