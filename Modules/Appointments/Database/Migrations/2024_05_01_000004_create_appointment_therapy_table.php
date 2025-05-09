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
        Schema::create('appointment_therapy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->foreignId('therapy_id'); // Sin restricción hasta tener tabla therapy
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('duration')->default(30); // en minutos
            $table->foreignId('therapist_id')->nullable(); // Sin restricción hasta tener tabla users con rol
            $table->timestamps();
            
            // Índice compuesto
            $table->index(['appointment_id', 'therapy_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_therapy');
    }
};