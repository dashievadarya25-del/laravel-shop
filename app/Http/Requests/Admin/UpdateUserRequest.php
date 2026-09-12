<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user ? $user->id : null),
            ],

            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['active', 'blocked'])],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Этот email уже занят другим пользователем.',
            'role_id.exists' => 'Выбранная роль не существует в системе.',
            'status.in' => 'Допустимые статусы: только active или blocked.',
            'password.confirmed' => 'Введенные пароли не совпадают.',
        ];
    }
}
