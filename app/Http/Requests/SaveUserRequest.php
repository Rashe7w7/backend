<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveUserRequest extends FormRequest
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
        $user = $this->route('user');
        $roleRules = [
            'role' => ['required', Rule::in(User::ROLES)],
            'profesor_id' => [
                Rule::requiredIf(in_array($this->input('role'), ['profesor', 'jefe_departamento'], true)),
                'nullable',
                'integer',
                'exists:profesor,id',
                Rule::unique('users', 'profesor_id')->ignore($user),
            ],
        ];

        if ($user) {
            return $roleRules;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            ...$roleRules,
        ];
    }

    public function messages(): array
    {
        return [
            'profesor_id.required' => 'Los roles de profesor y jefe deben vincularse con un profesor.',
            'profesor_id.unique' => 'Ese profesor ya está vinculado con otra cuenta.',
            'role.in' => 'El rol seleccionado no es válido.',
        ];
    }
}
