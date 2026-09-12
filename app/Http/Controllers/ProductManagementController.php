<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\ProductDto;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductManagementController extends Controller
{
    protected ProductManagementService $service;

    public function __construct(ProductManagementService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $dto = ProductDto::fromRequest($request);

        $this->service->create($dto);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Товар успешно создан');
    }

    public function edit(Product $product): View
    {
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        $dto = ProductDto::fromRequest($request);

        $this->service->update($product, $dto);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Товар успешно обновлен');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->service->delete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Товар успешно удален');
    }
}
