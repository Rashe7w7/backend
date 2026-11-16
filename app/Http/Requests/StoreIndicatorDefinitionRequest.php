<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreIndicatorDefinitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && collect(['administrador', 'ppa', 'jefe_departamento', 'rector', 'vicerrector', 'decano'])
            ->contains(fn (string $role) => $user->hasRole($role));
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
        ];
    }
}
