<?php

namespace App\Http\Requests;

use App\Services\AcademicScopeService;
use Illuminate\Foundation\Http\FormRequest;

class CreateAndAssignIndicadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        $type = (string) $this->input('especificacion');
        $key = match ($type) {
            'grupo' => 'grupo_id',
            'departamento' => 'departamento_id',
            'carrera' => 'carrera_id',
            'anno' => 'anio_academico_id',
            'facultad' => 'facultad_id',
            default => null,
        };

        return $key !== null && $this->user() !== null
            && app(AcademicScopeService::class)->canAccess($this->user(), $type, (int) $this->input($key));
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'categoria_indicador' => ['required', 'integer', 'exists:categoria_indicador,id'],
            'especificacion' => ['required', 'in:grupo,facultad,departamento,carrera,anno'],
            'curso_academico_id' => ['required_unless:especificacion,facultad', 'nullable', 'integer', 'exists:curso_academico,id'],
            'carrera_id' => ['required_if:especificacion,carrera', 'nullable', 'integer', 'exists:carrera,id'],
            'grupo_id' => ['required_if:especificacion,grupo', 'nullable', 'integer', 'exists:grupo,id'],
            'facultad_id' => ['required_if:especificacion,facultad', 'nullable', 'integer', 'exists:facultad,id'],
            'departamento_id' => ['required_if:especificacion,departamento', 'nullable', 'integer', 'exists:departamento,id'],
            'anio_academico_id' => ['required_if:especificacion,anno', 'nullable', 'integer', 'exists:anio_academico,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'curso_academico_id.required_unless' => 'Seleccione un curso académico para ese ámbito.',
            'carrera_id.required_if' => 'Seleccione la carrera del indicador.',
            'grupo_id.required_if' => 'Seleccione el grupo del indicador.',
            'facultad_id.required_if' => 'Seleccione la facultad del indicador.',
            'departamento_id.required_if' => 'Seleccione el departamento del indicador.',
            'anio_academico_id.required_if' => 'Seleccione el año académico del indicador.',
        ];
    }
}
