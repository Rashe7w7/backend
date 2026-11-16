<?php

namespace App\Http\Requests;

use App\Services\AcademicScopeService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportIndicatorsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && app(AcademicScopeService::class)->canAccess(
            $user,
            (string) $this->input('especificacion'),
            (int) $this->input('entidad_id'),
            true,
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'especificacion' => ['required', Rule::in(['grupo', 'departamento', 'carrera', 'anno', 'facultad'])],
            'entidad_id' => ['required', 'integer', 'min:1'],
            'curso_academico_id' => ['nullable', 'integer', 'exists:curso_academico,id'],
            'indicadores' => ['required', 'array', 'min:1'],
            'indicadores.*' => ['integer', 'distinct', 'exists:indicador,id'],
            'valores' => ['nullable', 'array'],
            'valores.*' => ['nullable', 'numeric'],
        ];
    }
}
