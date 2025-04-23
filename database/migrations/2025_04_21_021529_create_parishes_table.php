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
        Schema::create('parishes', function (Blueprint $table) {
            $table->id();
            $table->string('parroquia');
            $table->unsignedBigInteger('id_canton');

            // Índice y clave foránea
            $table->foreign('id_canton')
                  ->references('id')->on('cantons')
                  ->onUpdate('restrict')
                  ->onDelete('restrict');

            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parishes');
    }
};
