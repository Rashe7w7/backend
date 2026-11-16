<?php

namespace App\Http\Requests;

use App\Services\AcademicScopeService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIndicatorApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $type = (string) $this->input('especificacion');
        $entityId = (int) $this->input('entidad_id');

        return $user !== null && $entityId > 0
            && app(AcademicScopeService::class)->canAccess($user, $type, $entityId);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'indicador_id' => ['required', 'integer', 'exists:indicador,id'],
            'especificacion' => ['required', Rule::in(['grupo', 'departamento', 'carrera', 'anno', 'facultad'])],
            'entidad_id' => ['required', 'integer', 'min:1'],
            'curso_academico_id' => ['nullable', 'required_unless:especificacion,facultad', 'integer', 'exists:curso_academico,id'],
        ];
    }
}
