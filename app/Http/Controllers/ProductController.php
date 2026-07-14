<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\ProductFilterDto;
use App\Http\Requests\ProductFilterRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {
    }

    public function show(Product $product): Factory|View
    {
        $product = $this->productService->getProductPageData($product);

        return view('products.show', [
            'product' => $product,
        ]);
    }
    public function index(ProductFilterRequest $request): Factory|View
    {
        $dto = ProductFilterDto::fromRequest($request);
        $products = $this->productService->getProducts($dto);
        $maxPrice = $this->productService->getMaxProductPrice();

        return view('products.index', compact('products', 'dto', 'maxPrice'));
    }
}
