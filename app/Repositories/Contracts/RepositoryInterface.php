<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Contrato base para repositorios. El acceso a datos pasa siempre por aquí,
 * nunca desde controladores (ver Documento Técnico §3).
 */
interface RepositoryInterface
{
    public function all(array $columns = ['*']): Collection;

    public function find(int $id): ?Model;

    public function findOrFail(int $id): Model;

    public function create(array $attributes): Model;

    public function update(int $id, array $attributes): Model;

    public function delete(int $id): bool;
}
