<?php

namespace App\Services;

use App\Models\Reporte;
use App\Notifications\ReporteNotification;
use App\Support\Archivos;
use App\Support\DestinatariosEscolares;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;

class ReporteService extends BaseService
{
    /**
     * Crea un reporte de conducta con sus evidencias y notifica a los tutores.
     *
     * @param  array<int, UploadedFile>  $evidencias
     */
    public function crear(array $datos, array $evidencias = []): Reporte
    {
        $reporte = Reporte::create($datos);

        foreach ($evidencias as $archivo) {
            $path = $archivo->store('evidencias/'.$reporte->school_id, 'local');

            $reporte->evidencias()->create([
                'school_id' => $reporte->school_id,
                'tipo' => Archivos::tipo($archivo->getMimeType()),
                'path' => $path,
                'nombre_original' => $archivo->getClientOriginalName(),
                'mime' => $archivo->getMimeType(),
                'size' => $archivo->getSize(),
            ]);
        }

        $destinatarios = DestinatariosEscolares::tutoresYAdministrativos($reporte->alumno);

        if (! empty($destinatarios)) {
            Notification::send($destinatarios, new ReporteNotification($reporte));
        }

        return $reporte;
    }
}
