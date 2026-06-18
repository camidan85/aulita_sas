<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('clave', 20)->nullable();
            $table->string('nombre', 100);
            $table->timestamps();

            $table->unique(['school_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};
