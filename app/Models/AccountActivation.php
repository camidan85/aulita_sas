<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Activación del portal de padres. NO usa BelongsToSchool: el flujo es público
 * (sin tenant resuelto) y se busca por CURP a nivel plataforma.
 */
class AccountActivation extends Model
{
    protected $fillable = [
        'school_id', 'alumno_id', 'curp', 'apellido_paterno', 'nombre',
        'correo', 'telefono', 'token', 'expires_at', 'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function vigente(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }
}
