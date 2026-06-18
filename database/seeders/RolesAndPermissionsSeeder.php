<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Permisos base por módulo (se ampliarán por hito).
        $permissions = [
            'escuelas.gestionar',
            'usuarios.gestionar',
            'alumnos.ver', 'alumnos.crear', 'alumnos.editar', 'alumnos.eliminar',
            'tutores.gestionar',
            'docentes.gestionar',
            'grupos.gestionar', 'materias.gestionar', 'horarios.gestionar',
            'asistencias.registrar', 'asistencias.ver',
            'calificaciones.capturar', 'calificaciones.ver',
            'reportes.crear', 'reportes.ver',
            'avisos.crear', 'avisos.ver',
            'citas.gestionar',
            'bitacora.ver',
            'portal.ver',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $roles = [
            'super_admin' => Permission::pluck('name')->all(), // todos
            'director' => array_values(array_filter($permissions, fn ($p) => $p !== 'escuelas.gestionar')),
            'subdirector' => [
                'alumnos.ver', 'docentes.gestionar', 'grupos.gestionar', 'materias.gestionar',
                'horarios.gestionar', 'calificaciones.capturar', 'calificaciones.ver',
                'asistencias.ver', 'reportes.ver', 'avisos.crear', 'avisos.ver', 'bitacora.ver',
            ],
            'prefecto' => [
                'alumnos.ver', 'asistencias.registrar', 'asistencias.ver',
                'reportes.crear', 'reportes.ver', 'avisos.ver', 'citas.gestionar',
            ],
            'administrativo' => [
                'alumnos.ver', 'alumnos.crear', 'alumnos.editar', 'tutores.gestionar',
                'grupos.gestionar', 'avisos.crear', 'avisos.ver', 'asistencias.ver',
                'reportes.ver', 'citas.gestionar', 'bitacora.ver',
            ],
            'docente' => [
                'alumnos.ver', 'asistencias.registrar', 'asistencias.ver',
                'calificaciones.capturar', 'calificaciones.ver',
                'reportes.crear', 'reportes.ver', 'avisos.crear', 'avisos.ver',
            ],
            'padre' => ['portal.ver', 'avisos.ver'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($rolePermissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
