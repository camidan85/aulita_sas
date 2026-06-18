<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use App\Models\Concerns\Firmable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reporte extends Model
{
    use Auditable, BelongsToSchool, Firmable, HasFactory;

    protected $table = 'reportes';

    protected $fillable = [
        'school_id', 'alumno_id', 'profesor_id', 'tipo',
        'descripcion', 'fecha', 'hora', 'requiere_firma',
    ];

    protected $casts = [
        'fecha' => 'date',
        'requiere_firma' => 'boolean',
    ];

    public const TIPOS = [
        'mala_conducta' => 'Mala conducta',
        'incidencia_academica' => 'Incidencia académica',
        'incidencia_disciplinaria' => 'Incidencia disciplinaria',
        'aviso' => 'Aviso',
        'felicitacion' => 'Felicitación',
        'citatorio' => 'Citatorio',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profesor_id');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class);
    }

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }
}
