<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('solicitante_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('con_rol', ['docente', 'prefecto', 'director']);
            $table->foreignId('con_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo');
            $table->date('fecha_solicitada');
            $table->time('hora_solicitada')->nullable();
            $table->enum('estatus', ['solicitada', 'confirmada', 'reprogramada', 'cancelada', 'atendida'])->default('solicitada');
            $table->timestamps();

            $table->index(['school_id', 'estatus']);
            $table->index('alumno_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
