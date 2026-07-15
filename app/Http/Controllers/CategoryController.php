<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\ProductFilterDto;
use App\Http\Requests\ProductFilterRequest;
use App\Models\Category;
use App\Services\ProductService;

class CategoryController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function index()
    {
        $categories = Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category, ProductFilterRequest $request)
    {
        $dto = ProductFilterDto::fromRequest($request);

        $products = $this
            ->productService
            ->getProductsByCategoryId($category->id, $dto);

        return view('products.index', [
            'products' => $products,
            'category' => $category,
            'dto'      => $dto,
        ]);
    }
}
