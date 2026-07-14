<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Поиск
            'q' => ['nullable', 'string', 'max:255'],

            // Цена
            'min_price' => ['nullable', 'integer', 'min:0'],
            'max_price' => ['nullable', 'integer', 'min:0'],

            // В наличии (чекбокс)
            'in_stock' => ['nullable'],

            // Сортировка: только "белый список"
            'sort' => ['nullable', 'in:new,price_asc,price_desc,name_asc,name_desc,stock_asc,stock_desc'],

            // Кол-во на странице: только 10/25/50/100
            'per_page' => ['nullable', 'in:10,25,50,100'],
        ];
    }
}
