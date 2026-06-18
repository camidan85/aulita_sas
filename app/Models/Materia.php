<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use App\Models\Concerns\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use Auditable, BelongsToSchool, HasFactory;

    protected $table = 'materias';

    protected $fillable = ['school_id', 'clave', 'nombre'];
}
