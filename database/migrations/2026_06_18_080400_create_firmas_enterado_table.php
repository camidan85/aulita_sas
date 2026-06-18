<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('firmas_enterado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('firmable_type', 120);
            $table->unsignedBigInteger('firmable_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->string('ip', 45);
            $table->timestamps();

            $table->unique(['firmable_type', 'firmable_id', 'user_id'], 'firma_unica');
            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firmas_enterado');
    }
};
