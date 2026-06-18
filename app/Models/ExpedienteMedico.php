<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpedienteMedico extends Model
{
    use Auditable, BelongsToSchool, HasFactory;

    protected $table = 'expediente_medico';

    protected $fillable = [
        'school_id', 'alumno_id', 'tipo_sangre', 'alergias', 'medicamentos',
        'contacto_emergencia_nombre', 'contacto_emergencia_telefono',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }
}
