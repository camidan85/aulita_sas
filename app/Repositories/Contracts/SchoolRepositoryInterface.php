<?php

namespace App\Repositories\Contracts;

use App\Models\School;

interface SchoolRepositoryInterface extends RepositoryInterface
{
    public function findBySlug(string $slug): ?School;
}
