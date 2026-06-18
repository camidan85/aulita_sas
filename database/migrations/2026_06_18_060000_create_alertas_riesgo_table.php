<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas_riesgo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->enum('tipo', ['3_faltas_consecutivas', '5_faltas_mes', '10_retardos']);
            $table->string('detalle', 255)->nullable();
            $table->boolean('atendida')->default(false);
            $table->timestamp('generada_en');
            $table->timestamps();

            $table->index(['school_id', 'alumno_id']);
            $table->index(['tipo', 'atendida']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_riesgo');
    }
};
