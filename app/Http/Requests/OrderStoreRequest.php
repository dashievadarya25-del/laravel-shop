<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => [
                'required',
                'in:' . Order::PAYMENT_METHOD_CASH . ',' . Order::PAYMENT_METHOD_YOOKASSA
            ],
        ];
    }
}
