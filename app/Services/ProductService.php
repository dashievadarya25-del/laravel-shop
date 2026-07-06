<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProductListDto;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    private const array PER_PAGE_OPTIONS = [10, 25, 50, 100];

    /**
     * Возвращает список товаров с контролируемой пагинацией.
     */
    public function getProducts(ProductListDto $dto): LengthAwarePaginator
    {
        $query = Product::query();

        $perPage = in_array($dto->perPage, self::PER_PAGE_OPTIONS, true)
            ? $dto->perPage
            : 10;

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getProductPageData(Product $product): Product
    {
        // В будущем тут могут быть:
        // - проверка статуса (active/inactive)
        // - подгрузка связей
        // - вычисление скидок
        // - подготовка DTO / ViewModel

        return $product;
    }
}
