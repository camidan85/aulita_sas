<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CicloEscolar extends Model
{
    use Auditable, BelongsToSchool, HasFactory;

    protected $table = 'ciclos_escolares';

    protected $fillable = [
        'school_id', 'nombre', 'fecha_inicio', 'fecha_fin', 'vigente',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'vigente' => 'boolean',
    ];

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'ciclo_id');
    }

    public function periodos(): HasMany
    {
        return $this->hasMany(Periodo::class, 'ciclo_id');
    }
}
