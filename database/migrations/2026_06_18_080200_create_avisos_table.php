<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('titulo', 150);
            $table->text('contenido');
            $table->enum('alcance', ['escuela', 'grado', 'grupo', 'alumno']);
            $table->unsignedBigInteger('target_id')->nullable(); // referencia lógica, no FK
            $table->boolean('requiere_firma')->default(false);
            $table->foreignId('publicado_por')->constrained('users')->cascadeOnDelete();
            $table->timestamp('fecha_publicacion');
            $table->timestamps();

            $table->index(['school_id', 'alcance']);
            $table->index('fecha_publicacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avisos');
    }
};
