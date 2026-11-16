<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePpaAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdministrator() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'grupo_id' => ['required', 'integer', 'exists:grupo,id'],
            'curso_academico_id' => ['required', 'integer', 'exists:curso_academico,id'],
            'resolucion_decanal' => ['nullable', 'string', 'max:255'],
            'fecha_designacion' => ['required', 'date'],
            'activo' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return ['user_id.exists' => 'Seleccione un usuario PPA válido.'];
    }
}
