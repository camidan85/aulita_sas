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
        'hora_corte_faltas', 'timezone', 'umbral_riesgo_calif', 'qr_formato',
        'modulos_ocultos', 'settings', 'estatus',
    ];

    protected $casts = [
        'settings' => 'array',
        'modulos_ocultos' => 'array',
        'umbral_riesgo_calif' => 'decimal:2',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * ¿El módulo está activo para esta escuela? (no está en modulos_ocultos)
     */
    public function moduloActivo(string $clave): bool
    {
        return ! in_array($clave, $this->modulos_ocultos ?? [], true);
    }
}
