<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\ProductListRequest;
use Spatie\LaravelData\Data;

class ProductListDto extends Data
{
    public function __construct(
        public int $perPage,
    ) {
    }

    public static function fromRequest(ProductListRequest $request): self
    {
        return new self(
            perPage: (int) $request->validated('per_page') ?? 10
        );
    }
}
