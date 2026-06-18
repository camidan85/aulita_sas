<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('grado_id')->constrained('grados')->cascadeOnDelete();
            $table->string('nombre', 10);
            $table->foreignId('ciclo_id')->constrained('ciclos_escolares')->cascadeOnDelete();
            $table->foreignId('docente_titular_id')->nullable()->constrained('docentes')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'grado_id', 'nombre', 'ciclo_id'], 'grupos_unicos');
            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
