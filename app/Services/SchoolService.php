<?php

namespace App\Services;

use App\Models\School;
use App\Repositories\Contracts\SchoolRepositoryInterface;
use Illuminate\Support\Str;

class SchoolService extends BaseService
{
    public function __construct(protected SchoolRepositoryInterface $schools) {}

    public function create(array $data): School
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['nombre']);

        /** @var School */
        return $this->schools->create($data);
    }

    public function update(int $id, array $data): School
    {
        return $this->schools->update($id, $data);
    }
}
