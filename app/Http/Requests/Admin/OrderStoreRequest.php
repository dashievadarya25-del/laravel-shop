<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'payment_method' => ['required', Rule::in([Order::PAYMENT_METHOD_CASH, Order::PAYMENT_METHOD_CARD])],
            'status' => ['required', Rule::in(array_keys(Order::STATUS_LABELS))],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Необходимо выбрать пользователя.',
            'user_id.exists' => 'Выбранный пользователь не найден в базе данных.',
            'shipping_address.required' => 'Укажите адрес доставки.',
            'items.required' => 'Добавьте хотя бы один товар в заказ.',
            'items.*.product_id.exists' => 'Один из выбранных товаров не существует.',
            'items.*.quantity.min' => 'Количество товара не может быть меньше 1.',
        ];
    }
}
