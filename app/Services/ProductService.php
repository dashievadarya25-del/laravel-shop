<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProductFilterDto;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    private const array PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function getProductPageData(Product $product): Product
    {
        // В будущем тут могут быть:
        // - проверка статуса (active/inactive)
        // - подгрузка связей
        // - вычисление скидок
        // - подготовка DTO / ViewModel

        return $product;
    }

    public function getProducts(ProductFilterDto $dto): LengthAwarePaginator
    {
        //        dd($query);
        $query = Product::query();

        $perPage = in_array($dto->perPage, self::PER_PAGE_OPTIONS, true)
            ? $dto->perPage
            : 10;

        /**
         * 1) Поиск (базовый)
         * Ищем по name и sku.
         *
         * Важно: оборачиваем OR-условия в where(function),
         * чтобы они не ломали остальные фильтры.
         */
        if ($dto->q) {
            $q = $dto->q;

            $query->where(function ($subQuery) use ($q) {
                $subQuery
                    ->where('name', 'like', '%' . $q . '%')
                    ->orWhere('sku', 'like', '%' . $q . '%');
            });
        }

        /**
         * 2) Фильтр по цене
         */
        if ($dto->minPrice !== null) {
            $query->where('price', '>=', $dto->minPrice);
        }

        if ($dto->maxPrice !== null) {
            $query->where('price', '<=', $dto->maxPrice);
        }

        /**
         * 3) Только в наличии
         */
        if ($dto->inStock) {
            $query->where('stock', '>', 0);
        }

        /**
         * 4) Сортировка (только через whitelist)
         */
        switch ($dto->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;

            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;

            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;

            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;

            case 'new':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        /**
         * Вторичная сортировка (чтобы порядок был стабильным, если значения одинаковые)
         * Например: два товара с одинаковой ценой.
         */
        $query->orderByDesc('id');

        /**
         * 5) Пагинация + сохранение query string
         * withQueryString() — чтобы при переключении страниц фильтры НЕ сбрасывались.
         */
        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getMaxProductPrice(): int
    {
        return (int) (Product::query()->max('price') ?? 0);
    }
}
