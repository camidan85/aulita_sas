<?php

namespace App\Http\Requests;

use App\Tenancy\TenantManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlumnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('alumnos.editar') ?? false;
    }

    public function rules(): array
    {
        $schoolId = app(TenantManager::class)->schoolId();
        $alumnoId = $this->route('alumno')->id;

        return [
            'matricula' => [
                'required', 'string', 'max:30',
                Rule::unique('alumnos')->ignore($alumnoId)->where(fn ($q) => $q->where('school_id', $schoolId)),
            ],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'curp' => [
                'required', 'string', 'size:18',
                Rule::unique('alumnos')->ignore($alumnoId)->where(fn ($q) => $q->where('school_id', $schoolId)),
            ],
            'fecha_nacimiento' => ['nullable', 'date'],
            'sexo' => ['nullable', Rule::in(['M', 'F', 'X'])],
            'correo' => ['nullable', 'email', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'grupo_id' => ['nullable', Rule::exists('grupos', 'id')->where(fn ($q) => $q->where('school_id', $schoolId))],
            'estatus' => ['required', Rule::in(['activo', 'baja', 'egresado', 'suspendido'])],
        ];
    }
}
