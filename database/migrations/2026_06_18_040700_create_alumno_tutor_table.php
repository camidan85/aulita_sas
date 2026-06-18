<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumno_tutor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained('tutores')->cascadeOnDelete();
            $table->enum('tipo', ['principal', 'secundario']);
            $table->timestamps();

            // Un alumno no repite tutor; y solo un principal y un secundario por alumno.
            $table->unique(['alumno_id', 'tutor_id']);
            $table->unique(['alumno_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno_tutor');
    }
};
