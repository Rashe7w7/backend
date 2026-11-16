<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAlumnoAyudanteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ($this->user()?->isAdministrator() ?? false) || ($this->user()?->hasRole('ppa') ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'alumno_id' => ['required', 'integer', 'exists:estudiante,id'],
            'grupo_id' => ['required', 'integer', 'exists:grupo,id'],
            'asignatura_id' => ['required', 'integer', 'exists:asignatura,id'],
            'profesor_id' => ['required', 'integer', 'exists:profesor,id'],
            'curso_academico_id' => ['required', 'integer', 'exists:curso_academico,id'],
            'fecha_designacion' => ['required', 'date'],
            'estado' => ['required', 'in:activo,inactivo'],
        ];
    }
}
