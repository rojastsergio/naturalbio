<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplementsTable extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up()
    {
        Schema::create('supplements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('clinic_id')->unsigned()->index(); // Para multitenancy
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->decimal('default_price', 10, 2)->default(0.00);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->json('media')->nullable(); // Almacenamiento de múltiples archivos multimedia
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
            $table->softDeletes(); // Para mantener historial
            
            // Restricción de clave foránea para clinic_id
            $table->foreign('clinic_id')->references('id')->on('clinics');
        });
    }

    /**
     * Revertir la migración.
     */
    public function down()
    {
        Schema::dropIfExists('supplements');
    }
}