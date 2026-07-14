<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\ProductFilterRequest;
use Spatie\LaravelData\Data;

class ProductFilterDto extends Data
{
    public function __construct(
        public ?string $q,
        public ?int $minPrice,
        public ?int $maxPrice,
        public bool $inStock,
        public string $sort,
        public int $perPage,
    ) {
    }

    public static function fromRequest(ProductFilterRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            q: $validated['q'] ?? null,
            minPrice: isset($validated['min_price']) ? (int) $validated['min_price'] : null,
            maxPrice: isset($validated['max_price']) ? (int) $validated['max_price'] : null,

            // boolean() удобно для чекбоксов: вернёт true/false
            inStock: $request->boolean('in_stock'),

            // Дефолты
            sort: $validated['sort'] ?? 'new',
            perPage: isset($validated['per_page']) ? (int) $validated['per_page'] : 10,
        );
    }
}
