<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\ProductDto;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Category;
use App\Models\Product; // Добавили импорт для категорий
use App\Services\ProductManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View; // Добавили импорт для возврата страниц

class ProductManagementController extends Controller
{
    protected ProductManagementService $service;

    public function __construct(ProductManagementService $service)
    {
        $this->service = $service;
    }

    /**
     * Показ страницы создания нового товара.
     */
    public function create(): View
    {
        // Извлекаем все категории, чтобы заполнить выпадающий список в _form.blade.php
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $dto = ProductDto::fromRequest($request);

        $this->service->create($dto);

        return redirect()->route('products.index');
    }

    /**
     * Показ страницы редактирования существующего товара.
     */
    public function edit(Product $product): View
    {
        // Извлекаем категории для выпадающего списка
        $categories = Category::all();

        // Передаем и категории, и текущий продукт для подстановки в _form.blade.php
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Обновление существующего товара.
     */
    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        $dto = ProductDto::fromRequest($request);

        $this->service->update($product, $dto);

        return redirect()->route('products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->service->delete($product);

        return redirect()->route('products.index');
    }
}
