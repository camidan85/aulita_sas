<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciclos_escolares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('nombre', 9);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('vigente')->default(false);
            $table->timestamps();

            $table->unique(['school_id', 'nombre']);
            $table->index(['school_id', 'vigente']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciclos_escolares');
    }
};
