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
        Schema::create('patient_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->string('type'); // 'supplement', 'therapy', 'questionnaire', 'custom'
            $table->foreignId('reference_id')->nullable(); // ID del elemento relacionado (suplemento, terapia, etc.)
            $table->json('tasks')->nullable(); // Tareas específicas en formato JSON
            $table->boolean('accepted')->default(false);
            $table->date('due_date')->nullable();
            $table->integer('progress')->default(0); // Porcentaje de progreso (0-100)
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // 'active', 'completed', 'cancelled'
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_recommendations');
    }
};