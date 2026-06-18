<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertaRiesgo extends Model
{
    use Auditable, BelongsToSchool, HasFactory;

    protected $table = 'alertas_riesgo';

    protected $fillable = [
        'school_id', 'alumno_id', 'tipo', 'detalle', 'atendida', 'generada_en',
    ];

    protected $casts = [
        'atendida' => 'boolean',
        'generada_en' => 'datetime',
    ];

    public const TIPO_3_FALTAS = '3_faltas_consecutivas';

    public const TIPO_5_FALTAS_MES = '5_faltas_mes';

    public const TIPO_10_RETARDOS = '10_retardos';

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function descripcion(): string
    {
        return match ($this->tipo) {
            self::TIPO_3_FALTAS => '3 faltas consecutivas',
            self::TIPO_5_FALTAS_MES => '5 faltas en el mes',
            self::TIPO_10_RETARDOS => '10 retardos acumulados',
            default => $this->tipo,
        };
    }
}
