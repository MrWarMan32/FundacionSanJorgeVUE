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
        Schema::create('doctor_therapies', function (Blueprint $table) {
            $table->id();
            // Claves foráneas
            $table->unsignedBigInteger('id_doctor');
            $table->unsignedBigInteger('id_therapy');

            // Relaciones
            $table->foreign('id_doctor')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_therapy')->references('id')->on('therapies')->onDelete('cascade');

            // Índice único para evitar duplicados
            $table->unique(['id_doctor', 'id_therapy']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_therapies');
    }
};
