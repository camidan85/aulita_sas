<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use App\Models\Concerns\Firmable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aviso extends Model
{
    use Auditable, BelongsToSchool, Firmable, HasFactory;

    protected $table = 'avisos';

    protected $fillable = [
        'school_id', 'titulo', 'contenido', 'alcance', 'target_id',
        'requiere_firma', 'publicado_por', 'fecha_publicacion',
    ];

    protected $casts = [
        'requiere_firma' => 'boolean',
        'fecha_publicacion' => 'datetime',
    ];

    public const ALCANCES = [
        'escuela' => 'Toda la escuela',
        'grado' => 'Grado',
        'grupo' => 'Grupo',
        'alumno' => 'Alumno',
    ];

    public function publicadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publicado_por');
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(AvisoAdjunto::class);
    }

    public function alcanceLabel(): string
    {
        return self::ALCANCES[$this->alcance] ?? $this->alcance;
    }
}
