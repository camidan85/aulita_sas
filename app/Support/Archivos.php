<?php

namespace App\Support;

class Archivos
{
    /**
     * Clasifica un archivo por su MIME para la columna `tipo` de evidencias.
     */
    public static function tipo(?string $mime): string
    {
        return match (true) {
            str_starts_with((string) $mime, 'image/') => 'imagen',
            $mime === 'application/pdf' => 'pdf',
            str_starts_with((string) $mime, 'video/') => 'video',
            default => 'documento',
        };
    }
}
