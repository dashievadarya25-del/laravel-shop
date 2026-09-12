<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in(array_keys(Order::STATUS_LABELS))
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Необходимо выбрать новый статус заказа.',
            'status.in'       => 'Выбранный статус не существует в системе.',
        ];
    }
}
