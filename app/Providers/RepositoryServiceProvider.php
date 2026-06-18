<?php

namespace App\Providers;

use App\Repositories\Contracts\SchoolRepositoryInterface;
use App\Repositories\Eloquent\SchoolRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Enlaza los contratos de repositorio con su implementación Eloquent.
 * Cada nueva entidad registra aquí su binding.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        SchoolRepositoryInterface::class => SchoolRepository::class,
    ];
}
