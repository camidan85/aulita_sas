<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grado;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\School;
use App\Models\Tutor;
use App\Tenancy\TenantManager;
use Illuminate\Database\Seeder;

/**
 * Genera un dataset coherente para la escuela "Colegio Demo":
 * ciclo, periodos, grados, grupos, materias, docentes, alumnos y tutores.
 */
class DemoSchoolSeeder extends Seeder
{
    public function run(): void
    {
        $escuela = School::firstWhere('slug', 'colegio-demo');

        if (! $escuela) {
            $this->command?->warn('No existe Colegio Demo; corre primero DatabaseSeeder.');

            return;
        }

        // Fija el tenant para que el school_id se asigne y filtre automáticamente.
        $tenant = app(TenantManager::class);
        $tenant->setSchoolId($escuela->id);

        if (Alumno::exists()) {
            $this->command?->info('Datos demo ya presentes; se omite.');

            return;
        }

        $ciclo = CicloEscolar::create([
            'nombre' => '2025-2026',
            'fecha_inicio' => '2025-08-01',
            'fecha_fin' => '2026-07-15',
            'vigente' => true,
        ]);

        foreach (['trimestre_1' => ['2025-08-01', '2025-11-15'],
            'trimestre_2' => ['2025-11-16', '2026-03-15'],
            'trimestre_3' => ['2026-03-16', '2026-07-15']] as $nombre => [$ini, $fin]) {
            Periodo::create([
                'nombre' => $nombre,
                'ciclo_id' => $ciclo->id,
                'fecha_inicio' => $ini,
                'fecha_fin' => $fin,
            ]);
        }

        $docentes = Docente::factory()->count(6)->create(['school_id' => $escuela->id]);

        Materia::factory()->count(6)->create(['school_id' => $escuela->id]);

        $grupos = collect();
        foreach ([1, 2, 3] as $nivel) {
            $grado = Grado::create([
                'nombre' => "{$nivel}°",
                'nivel' => $nivel,
            ]);

            foreach (['A', 'B'] as $letra) {
                $grupos->push(Grupo::create([
                    'grado_id' => $grado->id,
                    'nombre' => $letra,
                    'ciclo_id' => $ciclo->id,
                    'docente_titular_id' => $docentes->random()->id,
                ]));
            }
        }

        // Alumnos repartidos en grupos, cada uno con un tutor principal.
        Alumno::factory()->count(24)->create(['school_id' => $escuela->id])
            ->each(function (Alumno $alumno) use ($grupos, $escuela) {
                $alumno->update(['grupo_id' => $grupos->random()->id]);

                $tutor = Tutor::factory()->create(['school_id' => $escuela->id]);
                $alumno->tutores()->attach($tutor->id, [
                    'school_id' => $escuela->id,
                    'tipo' => 'principal',
                ]);
            });

        $this->command?->info('Colegio Demo poblado: '.Alumno::count().' alumnos, '.$grupos->count().' grupos.');
    }
}
