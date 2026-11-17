<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlumnoAyudanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-student-helpers') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'alumno_id' => ['required', 'integer', 'exists:estudiante,id'],
            'profesor_asignatura_id' => [
                'required',
                'integer',
                Rule::exists('profesor_asignatura', 'id')
                    ->where(fn (Builder $query): Builder => $query->whereNotNull('curso_academico_id')),
                Rule::unique('estudiante_ayudante', 'profesor_asignatura_id')
                    ->where(fn (Builder $query): Builder => $query->where('alumno_id', $this->integer('alumno_id'))),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'alumno_id.required' => 'Seleccione un estudiante.',
            'alumno_id.exists' => 'El estudiante seleccionado no existe.',
            'profesor_asignatura_id.required' => 'Seleccione la asignatura de la designación.',
            'profesor_asignatura_id.exists' => 'La asignatura seleccionada no tiene una asignación docente válida para un curso académico.',
            'profesor_asignatura_id.unique' => 'El estudiante ya está designado como alumno ayudante en esa asignatura y curso.',
        ];
    }
}
