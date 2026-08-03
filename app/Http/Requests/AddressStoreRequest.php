<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddressStoreRequest extends FormRequest
{
    /**
     * Разрешить ли пользователю выполнять этот запрос.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Правила валидации для полей адреса.
     */
    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'house' => ['required', 'string', 'max:50'],
        ];
    }

    /**
     * Кастомные сообщения об ошибках (опционально, для русского языка).
     */
    public function messages(): array
    {
        return [
            'city.required' => 'Поле "Город" обязательно для заполнения.',
            'city.max' => 'Название города не должно превышать 255 символов.',

            'street.required' => 'Поле "Улица" обязательно для заполнения.',
            'street.max' => 'Название улицы не должно превышать 255 символов.',

            'house.required' => 'Поле "Дом / Кв" обязательно для заполнения.',
            'house.max' => 'Номер дома или квартиры не должен превышать 50 символов.',
        ];
    }
}
