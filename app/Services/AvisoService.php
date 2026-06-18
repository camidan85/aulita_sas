<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Aviso;
use App\Models\User;
use App\Notifications\AvisoNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;

class AvisoService extends BaseService
{
    /**
     * Publica un aviso segmentado, guarda adjuntos y notifica al público objetivo.
     *
     * @param  array<int, UploadedFile>  $adjuntos
     */
    public function publicar(array $datos, array $adjuntos = []): Aviso
    {
        $aviso = Aviso::create($datos);

        foreach ($adjuntos as $archivo) {
            $path = $archivo->store('avisos/'.$aviso->school_id, 'local');

            $aviso->adjuntos()->create([
                'school_id' => $aviso->school_id,
                'path' => $path,
                'nombre_original' => $archivo->getClientOriginalName(),
                'mime' => $archivo->getMimeType(),
                'size' => $archivo->getSize(),
            ]);
        }

        $destinatarios = $this->destinatarios($aviso);

        if (! empty($destinatarios)) {
            Notification::send($destinatarios, new AvisoNotification($aviso));
        }

        return $aviso;
    }

    /**
     * Resuelve los tutores del público objetivo + administrativos (RN avisos).
     *
     * @return array<int, object>
     */
    private function destinatarios(Aviso $aviso): array
    {
        $tutores = $this->alumnosObjetivo($aviso)
            ->with('tutores')
            ->get()
            ->flatMap(fn (Alumno $a) => $a->tutores)
            ->unique('id')
            ->values()
            ->all();

        $administrativos = User::where('school_id', $aviso->school_id)
            ->role('administrativo')
            ->get()
            ->all();

        return array_merge($tutores, $administrativos);
    }

    private function alumnosObjetivo(Aviso $aviso): Builder
    {
        return match ($aviso->alcance) {
            'grado' => Alumno::whereHas('grupo', fn ($q) => $q->where('grado_id', $aviso->target_id)),
            'grupo' => Alumno::where('grupo_id', $aviso->target_id),
            'alumno' => Alumno::whereKey($aviso->target_id),
            default => Alumno::query(), // escuela completa
        };
    }
}
