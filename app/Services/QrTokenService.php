<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\QrToken;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrTokenService extends BaseService
{
    private const PREFIJO = 'AULITA:';

    /**
     * Devuelve el token activo del alumno; lo genera si no existe.
     */
    public function tokenActivo(Alumno $alumno): QrToken
    {
        return $alumno->qrTokenActivo()->first() ?? $this->generar($alumno);
    }

    /**
     * Rota el token: desactiva los anteriores y crea uno nuevo.
     */
    public function generar(Alumno $alumno): QrToken
    {
        $alumno->qrTokens()->where('activo', true)->update(['activo' => false]);

        return $alumno->qrTokens()->create([
            'token' => Str::random(64),
            'activo' => true,
        ]);
    }

    /**
     * Contenido que se codifica en el QR.
     */
    public function contenido(QrToken $token): string
    {
        return self::PREFIJO.$token->token;
    }

    /**
     * Extrae el token crudo del contenido escaneado (o lo devuelve tal cual).
     */
    public function extraerToken(string $contenido): string
    {
        return Str::startsWith($contenido, self::PREFIJO)
            ? Str::after($contenido, self::PREFIJO)
            : $contenido;
    }

    /**
     * Resuelve el alumno a partir del contenido escaneado, dentro del tenant actual.
     */
    public function resolverAlumno(string $contenido): ?Alumno
    {
        $raw = $this->extraerToken($contenido);

        $qr = QrToken::where('token', $raw)->where('activo', true)->first();

        return $qr?->alumno;
    }

    public function svg(QrToken $token, int $size = 220): string
    {
        return QrCode::format('svg')->size($size)->margin(1)->generate($this->contenido($token));
    }
}
