<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->enum('tipo', ['curp', 'acta', 'certificado_primaria', 'comprobante_domicilio', 'otro']);
            $table->string('path', 255);
            $table->string('nombre_original', 255)->nullable();
            $table->string('mime', 100)->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->foreignId('subido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'alumno_id']);
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
