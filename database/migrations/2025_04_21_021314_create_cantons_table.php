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
        Schema::create('cantons', function (Blueprint $table) {
            $table->id();
            $table->string('name_canton');
            $table->unsignedBigInteger('id_province');
            
            // Índice y clave foránea
            $table->foreign('id_province')
                  ->references('id')->on('provinces')
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
        Schema::dropIfExists('cantons');
    }
};
