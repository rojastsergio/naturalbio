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
        Schema::create('doctor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics');
            $table->foreignId('doctor_id'); // Sin restricción foreign key hasta tener la tabla doctors
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('recurrence')->nullable(); // daily, weekly, monthly, none
            $table->date('recurrence_end')->nullable(); // Fecha fin de recurrencia
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            // Índices para mejorar consultas
            $table->index(['doctor_id', 'date']);
            $table->index(['clinic_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_availabilities');
    }
};