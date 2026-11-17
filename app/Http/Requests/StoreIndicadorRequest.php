<?php

namespace App\Http\Requests;

use App\EspecificacionIndicador;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIndicadorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('manage-indicator', $this->string('especificacion')->toString()) ?? false;
    }

    protected function failedAuthorization(): never
    {
        throw new AuthorizationException('No tienes permisos para realizar esta operación con este tipo de indicador');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:indicador,nombre'],
            'categoria_indicador_id' => ['required', 'integer', 'exists:categoria_indicador,id'],
            'especificacion' => ['required', Rule::enum(EspecificacionIndicador::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'categoria_indicador_id.required' => 'Seleccione un tipo de valor.',
            'categoria_indicador_id.exists' => 'El tipo de valor seleccionado no existe.',
            'especificacion.required' => 'Seleccione una especificación.',
        ];
    }
}
