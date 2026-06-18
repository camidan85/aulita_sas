<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Super Admin del SaaS (sin escuela).
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@aulita.test'],
            [
                'school_id' => null,
                'name' => 'Super Admin',
                'password' => Hash::make('Password#1'),
                'estatus' => 'activo',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        // Escuela demo + director (para pruebas locales).
        $escuela = School::firstOrCreate(
            ['slug' => 'colegio-demo'],
            [
                'nombre' => 'Colegio Demo',
                'telefono' => '5500000000',
                'correo' => 'contacto@colegiodemo.test',
                'hora_corte_faltas' => '07:15:00',
            ]
        );

        $director = User::firstOrCreate(
            ['email' => 'director@colegiodemo.test'],
            [
                'school_id' => $escuela->id,
                'name' => 'Director Demo',
                'password' => Hash::make('Password#1'),
                'estatus' => 'activo',
                'email_verified_at' => now(),
            ]
        );
        $director->assignRole('director');

        $this->call(DemoSchoolSeeder::class);
    }
}
