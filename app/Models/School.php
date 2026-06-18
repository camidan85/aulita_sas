<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tenant raíz del SaaS. NO usa BelongsToSchool (es la propia escuela).
 */
class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'slug', 'cct', 'logo', 'direccion', 'telefono', 'correo',
        'hora_corte_faltas', 'timezone', 'umbral_riesgo_calif', 'settings', 'estatus',
    ];

    protected $casts = [
        'settings' => 'array',
        'umbral_riesgo_calif' => 'decimal:2',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
