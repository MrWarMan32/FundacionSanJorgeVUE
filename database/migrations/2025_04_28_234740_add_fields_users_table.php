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

            // Información academica (usuario tipo doctor)
            $table->string('university_name')->nullable();
            $table->string('degree_title')->nullable();
            $table->date('graduation_year')->nullable();
            $table->string('speciality')->nullable();
            $table->text('certifications')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'university_name',
                'degree_title',
                'graduation_year',
                'speciality',
                'certifications',
            ]);
        });
    }
};
