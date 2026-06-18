<?php

namespace App\Services;

use App\Models\AccountActivation;
use App\Models\Alumno;
use App\Models\Tutor;
use App\Models\User;
use App\Notifications\ActivacionPortalNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ActivacionService extends BaseService
{
    /**
     * Inicia la activación: valida CURP + apellido contra un alumno (a nivel
     * plataforma, sin tenant) y envía el enlace de validación (24 h).
     *
     * @return AccountActivation|null null si no hay coincidencia única.
     */
    public function iniciar(array $datos): ?AccountActivation
    {
        $alumnos = Alumno::where('curp', strtoupper($datos['curp']))
            ->where('apellido_paterno', $datos['apellido_paterno'])
            ->get();

        if ($alumnos->count() !== 1) {
            return null;
        }

        $alumno = $alumnos->first();

        $activation = AccountActivation::create([
            'school_id' => $alumno->school_id,
            'alumno_id' => $alumno->id,
            'curp' => strtoupper($datos['curp']),
            'apellido_paterno' => $datos['apellido_paterno'],
            'nombre' => $datos['nombre'],
            'correo' => $datos['correo'],
            'telefono' => $datos['telefono'] ?? null,
            'token' => Str::random(64),
            'expires_at' => now()->addHours(24), // RN-P02
        ]);

        Notification::route('mail', $activation->correo)
            ->notify(new ActivacionPortalNotification($activation));

        return $activation;
    }

    /**
     * Completa la activación: crea la cuenta del padre, su perfil de tutor y
     * lo vincula al alumno. Marca la activación como usada.
     */
    public function completar(AccountActivation $activation, string $password): User
    {
        return DB::transaction(function () use ($activation, $password) {
            $padre = User::create([
                'school_id' => $activation->school_id,
                'name' => $activation->nombre,
                'email' => $activation->correo,
                'telefono' => $activation->telefono,
                'password' => Hash::make($password),
                'estatus' => 'activo',
                'email_verified_at' => now(),
            ]);
            $padre->assignRole('padre');

            $tutor = Tutor::create([
                'school_id' => $activation->school_id,
                'user_id' => $padre->id,
                'nombre' => $activation->nombre,
                'correo' => $activation->correo,
                'telefono' => $activation->telefono,
                'parentesco' => 'Tutor',
            ]);

            $alumno = $activation->alumno;
            $tipo = $alumno->tutores()->wherePivot('tipo', 'principal')->exists() ? 'secundario' : 'principal';

            $alumno->tutores()->attach($tutor->id, [
                'school_id' => $activation->school_id,
                'tipo' => $tipo,
            ]);

            $activation->update(['used_at' => now()]);

            return $padre;
        });
    }
}
