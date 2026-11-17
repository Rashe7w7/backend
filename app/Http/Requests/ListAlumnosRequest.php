<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListAlumnosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'grupo_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'carrera_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'per_page.integer' => 'La cantidad por página debe ser un número entero.',
            'per_page.min' => 'La cantidad por página debe ser al menos 1.',
            'per_page.max' => 'La cantidad por página no puede superar 100.',
            'page.integer' => 'La página debe ser un número entero.',
            'page.min' => 'La página debe ser al menos 1.',
            'grupo_id.integer' => 'El grupo debe tener un identificador entero.',
            'grupo_id.min' => 'El identificador del grupo debe ser positivo.',
            'carrera_id.integer' => 'La carrera debe tener un identificador entero.',
            'carrera_id.min' => 'El identificador de la carrera debe ser positivo.',
            'search.string' => 'La búsqueda debe ser texto.',
            'search.max' => 'La búsqueda no puede superar 255 caracteres.',
        ];
    }
}
