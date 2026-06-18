<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('nombre', 30);
            $table->unsignedTinyInteger('nivel');
            $table->timestamps();

            $table->unique(['school_id', 'nivel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grados');
    }
};
