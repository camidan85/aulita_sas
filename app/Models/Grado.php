<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grado extends Model
{
    use Auditable, BelongsToSchool, HasFactory;

    protected $table = 'grados';

    protected $fillable = ['school_id', 'nombre', 'nivel'];

    protected $casts = ['nivel' => 'integer'];

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class);
    }
}
