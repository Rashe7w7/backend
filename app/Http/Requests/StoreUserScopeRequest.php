<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserScopeRequest extends FormRequest
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
        $table = match ($this->input('ambito_tipo')) {
            'facultad' => 'facultad',
            'departamento' => 'departamento',
            'carrera' => 'carrera',
            'anno' => 'anio_academico',
            'grupo' => 'grupo',
            default => 'facultad',
        };

        return [
            'ambito_tipo' => ['required', Rule::in(['facultad', 'departamento', 'carrera', 'anno', 'grupo'])],
            'ambito_id' => ['required', 'integer', Rule::exists($table, 'id')],
            'puede_exportar' => ['required', 'boolean'],
        ];
    }
}
