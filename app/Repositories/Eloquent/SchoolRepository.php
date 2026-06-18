<?php

namespace App\Repositories\Eloquent;

use App\Models\School;
use App\Repositories\Contracts\SchoolRepositoryInterface;

class SchoolRepository extends BaseRepository implements SchoolRepositoryInterface
{
    public function __construct(School $model)
    {
        parent::__construct($model);
    }

    public function findBySlug(string $slug): ?School
    {
        return $this->model->newQuery()->where('slug', $slug)->first();
    }
}
