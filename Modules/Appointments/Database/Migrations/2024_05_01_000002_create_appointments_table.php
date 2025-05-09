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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics');
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('doctor_id')->nullable(); // Se implementará cuando esté el módulo de doctores
            $table->foreignId('appointment_type_id')->nullable()->constrained('appointment_types');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status')->default('scheduled'); // scheduled, completed, cancelled, no-show
            $table->decimal('total_price', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('doctor_notes')->nullable(); // Notas privadas para el doctor
            $table->boolean('emergency')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para mejorar consultas
            $table->index(['patient_id', 'start_time']);
            $table->index(['doctor_id', 'start_time']);
            $table->index(['clinic_id', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};