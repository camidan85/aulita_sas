<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    use Auditable, BelongsToSchool, HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'school_id', 'alumno_id', 'solicitante_user_id', 'con_rol', 'con_user_id',
        'motivo', 'fecha_solicitada', 'hora_solicitada', 'estatus',
    ];

    protected $casts = [
        'fecha_solicitada' => 'date',
    ];

    public const ROLES = ['docente' => 'Docente', 'prefecto' => 'Prefecto', 'director' => 'Director'];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_user_id');
    }
}
