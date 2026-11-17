<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportPreviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage-users') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(['estudiantes', 'profesores', 'asignaturas'])],
            'archivo' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'Seleccione el tipo de datos que desea importar.',
            'archivo.required' => 'Seleccione un archivo CSV o XLSX.',
            'archivo.mimes' => 'El archivo debe ser CSV o XLSX.',
            'archivo.max' => 'El archivo no puede superar 10 MB.',
        ];
    }
}
