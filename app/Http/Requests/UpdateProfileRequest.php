<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Убираем "+" из номера телефона перед валидацией
        if ($this->has('phone')) {
            $this->merge([
                'phone' => str_replace('+', '', $this->phone)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'first_name' => [
                'nullable',
                'string',
                'max:255',
                'min:2',
                'regex:/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u' // только буквы, пробелы и дефисы
            ],
            'last_name' => [
                'nullable',
                'string',
                'max:255',
                'min:2',
                'regex:/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u'
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::id()) // игнорировать текущего пользователя
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{10,15}$/', // от 10 до 15 цифр
                Rule::unique('users', 'phone')->ignore(Auth::id())
            ],
        ];
    }
}
