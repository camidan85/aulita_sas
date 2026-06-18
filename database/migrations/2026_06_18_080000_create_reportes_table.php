<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('profesor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('tipo', [
                'mala_conducta', 'incidencia_academica', 'incidencia_disciplinaria',
                'aviso', 'felicitacion', 'citatorio',
            ]);
            $table->text('descripcion');
            $table->date('fecha');
            $table->time('hora')->nullable();
            $table->boolean('requiere_firma')->default(false);
            $table->timestamps();

            $table->index(['school_id', 'alumno_id']);
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
