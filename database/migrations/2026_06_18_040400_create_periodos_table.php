<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->enum('nombre', ['trimestre_1', 'trimestre_2', 'trimestre_3']);
            $table->foreignId('ciclo_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamps();

            $table->unique(['school_id', 'nombre', 'ciclo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos');
    }
};
