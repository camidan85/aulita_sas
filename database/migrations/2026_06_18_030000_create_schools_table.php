<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('slug', 150)->unique();
            $table->string('cct', 20)->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 150)->nullable();
            $table->time('hora_corte_faltas')->default('07:15:00');
            $table->string('timezone', 40)->default('America/Mexico_City');
            $table->decimal('umbral_riesgo_calif', 4, 2)->default(6.00);
            $table->json('settings')->nullable();
            $table->enum('estatus', ['activa', 'suspendida', 'baja'])->default('activa');
            $table->timestamps();

            $table->index('estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
