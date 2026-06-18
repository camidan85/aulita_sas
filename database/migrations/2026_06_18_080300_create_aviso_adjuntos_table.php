<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aviso_adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('aviso_id')->constrained('avisos')->cascadeOnDelete();
            $table->string('path', 255);
            $table->string('nombre_original', 255)->nullable();
            $table->string('mime', 100)->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->timestamps();

            $table->index('aviso_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aviso_adjuntos');
    }
};
