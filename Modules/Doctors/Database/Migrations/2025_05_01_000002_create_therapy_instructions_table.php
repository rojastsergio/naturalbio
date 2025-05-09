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
        Schema::create('therapy_instructions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained();
            $table->foreignId('clinic_id')->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('instructions');
            $table->json('body_areas')->nullable(); // Para mapeo a silueta interactiva
            $table->json('media')->nullable(); // Imágenes o documentos relacionados
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapy_instructions');
    }
};