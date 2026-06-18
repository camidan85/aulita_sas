<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alumno extends Model
{
    use Auditable, BelongsToSchool, HasFactory, SoftDeletes;

    protected $table = 'alumnos';

    protected $fillable = [
        'school_id', 'grupo_id', 'matricula', 'nombre',
        'apellido_paterno', 'apellido_materno', 'curp', 'fecha_nacimiento',
        'sexo', 'correo', 'telefono', 'fotografia', 'estatus',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function tutores(): BelongsToMany
    {
        return $this->belongsToMany(Tutor::class, 'alumno_tutor')
            ->withPivot('tipo')
            ->withTimestamps();
    }

    public function expedienteMedico(): HasOne
    {
        return $this->hasOne(ExpedienteMedico::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class);
    }

    public function qrTokens(): HasMany
    {
        return $this->hasMany(QrToken::class);
    }

    public function qrTokenActivo(): HasOne
    {
        return $this->hasOne(QrToken::class)->where('activo', true);
    }

    public function tutorPrincipal(): ?Tutor
    {
        return $this->tutores->firstWhere('pivot.tipo', 'principal');
    }

    public function nombreCompleto(): string
    {
        return trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}");
    }
}
