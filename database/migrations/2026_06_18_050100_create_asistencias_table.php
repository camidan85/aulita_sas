<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora')->nullable();
            $table->enum('estatus', ['presente', 'retardo', 'falta', 'falta_pendiente', 'justificada']);
            $table->enum('origen', ['qr', 'automatico', 'manual']);
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip', 45)->nullable();
            $table->string('dispositivo', 255)->nullable();
            $table->string('observaciones', 255)->nullable();
            $table->timestamps();

            $table->unique(['alumno_id', 'fecha']); // RN-AS01
            $table->index(['school_id', 'fecha']);
            $table->index('estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
