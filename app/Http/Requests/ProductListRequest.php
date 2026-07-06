<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации параметров пагинации.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'in:10,25,50,100'],
        ];
    }
}
