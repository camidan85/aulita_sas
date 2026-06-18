<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Plantilla del contenido del QR (placeholders: {matricula}, {curp}, {id}, etc.)
            $table->string('qr_formato', 100)->default('{matricula}')->after('umbral_riesgo_calif');
            // Módulos desactivados para la escuela (array de claves).
            $table->json('modulos_ocultos')->nullable()->after('qr_formato');
        });

        Schema::table('alumnos', function (Blueprint $table) {
            // Contenido del QR materializado (derivado de la plantilla de la escuela).
            $table->string('codigo_qr', 120)->nullable()->after('matricula');
            $table->unique(['school_id', 'codigo_qr']);
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropUnique(['school_id', 'codigo_qr']);
            $table->dropColumn('codigo_qr');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['qr_formato', 'modulos_ocultos']);
        });
    }
};
