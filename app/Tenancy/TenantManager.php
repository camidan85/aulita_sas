<?php

namespace App\Tenancy;

use App\Models\School;

/**
 * Mantiene el tenant (school_id) activo durante el ciclo de la petición.
 * El TenantScope lo lee para filtrar automáticamente toda consulta de dominio.
 */
class TenantManager
{
    protected ?int $schoolId = null;

    protected bool $bypassed = false;

    protected ?School $school = null;

    public function setSchoolId(?int $schoolId): void
    {
        $this->schoolId = $schoolId;
        $this->school = null; // invalida la escuela cacheada
    }

    /**
     * Escuela activa (cacheada por petición). Null para Super Admin / sin tenant.
     */
    public function school(): ?School
    {
        if (! $this->hasTenant()) {
            return null;
        }

        if ($this->school && $this->school->id === $this->schoolId) {
            return $this->school;
        }

        return $this->school = School::find($this->schoolId);
    }

    public function schoolId(): ?int
    {
        return $this->schoolId;
    }

    public function hasTenant(): bool
    {
        return $this->schoolId !== null;
    }

    public function isBypassed(): bool
    {
        return $this->bypassed;
    }

    /**
     * Ejecuta un callback sin el filtro de tenant (solo Super Admin / tareas de sistema).
     * Debe usarse de forma explícita y auditada.
     */
    public function bypass(callable $callback): mixed
    {
        $previous = $this->bypassed;
        $this->bypassed = true;

        try {
            return $callback();
        } finally {
            $this->bypassed = $previous;
        }
    }
}
