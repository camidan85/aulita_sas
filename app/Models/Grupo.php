<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    use Auditable, BelongsToSchool, HasFactory;

    protected $table = 'grupos';

    protected $fillable = [
        'school_id', 'grado_id', 'nombre', 'ciclo_id', 'docente_titular_id',
    ];

    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class);
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_id');
    }

    public function docenteTitular(): BelongsTo
    {
        return $this->belongsTo(Docente::class, 'docente_titular_id');
    }

    public function alumnos(): HasMany
    {
        return $this->hasMany(Alumno::class);
    }

    public function nombreCompleto(): string
    {
        return trim(($this->grado?->nivel ?? '').$this->nombre);
    }
}
