<?php

namespace App\Tenancy;

/**
 * Mantiene el tenant (school_id) activo durante el ciclo de la petición.
 * El TenantScope lo lee para filtrar automáticamente toda consulta de dominio.
 */
class TenantManager
{
    protected ?int $schoolId = null;

    protected bool $bypassed = false;

    public function setSchoolId(?int $schoolId): void
    {
        $this->schoolId = $schoolId;
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
