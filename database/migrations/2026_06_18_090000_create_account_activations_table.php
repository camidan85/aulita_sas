<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->string('curp', 18);
            $table->string('apellido_paterno', 100);
            $table->string('nombre', 150);
            $table->string('correo', 150);
            $table->string('telefono', 20)->nullable();
            $table->string('token', 128)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'alumno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_activations');
    }
};
