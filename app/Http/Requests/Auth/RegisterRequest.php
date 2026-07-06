<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Возвращает сообщение об ошибке на отдельное взятое правило
     *
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Введите email',
            'email.email' => 'Неверный формат email',
            'email.unique' => 'Пользователь с таким email уже зарегистрирован.',
            'password.min' => 'Пароль должен быть не менее 8 символов',
            'password.required' => 'Введите пароль',
            'first_name.required' => 'Введите имя',
            'last_name.required' => 'Введите фамилию'
        ];
    }
}
