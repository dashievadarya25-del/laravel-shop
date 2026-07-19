<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

readonly class CartItemsDto
{
    public function __construct(
        public int $quantity
    ) {
    }

    /**
     * Создание DTO из уже валидированного Form Request
     */
    public static function fromRequest(FormRequest $request): self
    {
        // Предотвращаем ошибку, если в store 'quantity' не был передан
        $quantity = $request->validated('quantity') ?? 1;

        return new self(
            quantity: (int) $quantity
        );
    }
}
