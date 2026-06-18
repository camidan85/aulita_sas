<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->nullOnDelete();
            $table->string('matricula', 30);
            $table->string('nombre', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100)->nullable();
            $table->string('curp', 18);
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['M', 'F', 'X'])->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('fotografia', 255)->nullable();
            $table->enum('estatus', ['activo', 'baja', 'egresado', 'suspendido'])->default('activo');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['school_id', 'matricula']);
            $table->unique(['school_id', 'curp']);
            $table->index(['school_id', 'grupo_id']);
            $table->index('estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
