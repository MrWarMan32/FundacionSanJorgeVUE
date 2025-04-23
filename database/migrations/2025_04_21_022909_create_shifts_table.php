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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            
            //relaciones
            $table->unsignedBigInteger('id_patient');
            $table->unsignedBigInteger('id_doctor');
            $table->unsignedBigInteger('id_therapy');
            $table->unsignedBigInteger('id_appointment');

            //Datos para generacion de citas
            $table->boolean('is_recurring')->default(false); // Indica si la cita es recurrente
            $table->unsignedBigInteger('id_parent_shift')->nullable(); // Relación con la cita principal en caso de ser recurrente

            // Datos de la cita
            $table->enum('status', ['pendiente', 'completada'])->default('pendiente');
            $table->text('notes')->nullable();
            $table->date('date')->nullable(); 
            $table->timestamp('start_time'); 
            $table->timestamp('end_time'); 
            
            // Definir las claves foráneas
            $table->foreign('id_patient')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_appointment')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('id_doctor')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_therapy')->references('id')->on('therapies')->onDelete('cascade');
            $table->foreign('id_parent_shift')->references('id')->on('shifts')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
