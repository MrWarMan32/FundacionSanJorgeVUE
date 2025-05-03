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
        Schema::table('users', function (Blueprint $table) {

            // Relación con tabla addresses
            $table->unsignedBigInteger('id_address')->nullable()->after('id');
            $table->foreign('id_address')->references('id')->on('addresses')->nullOnDelete();

            // Datos personales
            $table->string('last_name', 100)->nullable();
            $table->unsignedBigInteger('id_card');
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable();
            $table->string('ethnicity', 100)->nullable();
            $table->boolean('disable_card')->default(0);
            $table->unsignedBigInteger('id_disable_card')->nullable();

            // Representante
            $table->string('representative_name')->nullable();
            $table->string('representative_last_name')->nullable();
            $table->unsignedBigInteger('representative_id_card')->nullable();
            $table->string('phone', 20)->nullable();

            // Información médica
            $table->json('disability_type')->nullable();
            $table->string('disability_level')->nullable();
            $table->integer('disability_grade')->nullable();
            $table->string('cause_disability')->nullable();
            $table->string('diagnosis')->nullable();

            // Rol y estado
            $table->enum('user_type', ['admin', 'doctor', 'usuario'])->default('usuario');
            $table->enum('status', ['aspirante', 'paciente'])->nullable()->default('aspirante');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_address']);
            $table->dropColumn([
                'id_address',
                'last_name',
                'id_card',
                'gender',
                'birth_date',
                'age',
                'ethnicity',
                'disable_card',
                'id_disable_card',
                'representative_name',
                'representative_last_name',
                'representative_id_card',
                'phone',
                'disability_type',
                'disability_level',
                'disability_grade',
                'cause_disability',
                'diagnosis',
                'user_type',
                'status',
            ]);
        });
    }
};
