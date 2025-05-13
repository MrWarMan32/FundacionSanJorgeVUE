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

            //Relaciones
            $table->unsignedBigInteger('id_patient')->nullable();
            $table->unsignedBigInteger('id_doctor');
            $table->unsignedBigInteger('id_therapy');

            //Datos 
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('available')->default(true);

            // Definir las claves foráneas
            $table->foreign('id_patient')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_doctor')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_therapy')->references('id')->on('therapies')->onDelete('cascade');

            $table->timestamps();
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
