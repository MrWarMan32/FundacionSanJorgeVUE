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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->unsignedBigInteger('id_province')->nullable();
            $table->unsignedBigInteger('id_canton')->nullable();
            $table->unsignedBigInteger('id_parish')->nullable();
            $table->string('site')->nullable();
            $table->string('principal_street')->nullable();
            $table->string('secondary_street')->nullable();
            $table->string('reference')->nullable();

            // Relaciones
            $table->foreign('id_user')->references('id')->on('users')->nullOnDelete();
            $table->foreign('id_province')->references('id')->on('provinces')->nullOnDelete();
            $table->foreign('id_canton')->references('id')->on('cantons')->nullOnDelete();
            $table->foreign('id_parish')->references('id')->on('parishes')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
