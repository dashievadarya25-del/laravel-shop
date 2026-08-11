<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\Data;

class ProductDto extends Data
{
    public function __construct(
        public string $name,
        public float $price,
        public int $stock,
        public string $sku,
        public string $status,
        public ?int $category_id,
        public $image
    ) {
    }

    public static function fromRequest(FormRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            price: (float) $request->validated('price'),
            stock: (int) $request->validated('stock'),
            sku: $request->validated('sku'),
            status: $request->validated('status'),
            category_id: $request->validated('category_id') ? (int) $request->validated('category_id') : null,
            image: $request->file('image'),
        );
    }

    public function toProductData(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'status' => $this->status,
            'category_id' => $this->category_id,
        ];
    }
}
