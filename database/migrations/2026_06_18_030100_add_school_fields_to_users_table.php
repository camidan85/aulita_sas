<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')
                ->constrained('schools')->nullOnDelete();
            $table->string('telefono', 20)->nullable()->after('email');
            $table->enum('estatus', ['activo', 'inactivo'])->default('activo')->after('telefono');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');

            $table->index('school_id');
        });
        // RN-T03: email único global (ya viene UNIQUE de Breeze en la migración base).
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
            $table->dropColumn(['telefono', 'estatus', 'last_login_at']);
        });
    }
};
