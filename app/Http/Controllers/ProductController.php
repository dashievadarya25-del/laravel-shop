<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\ProductListDto;
use App\Http\Requests\ProductListRequest;
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
    public function index(ProductListRequest $request): Factory|View
    {
        $dto = ProductListDto::fromRequest($request);
        $products = $this->productService->getProducts($dto);

        return view('products.index', [
            'products' => $products,
            'dto'      => $dto,
        ]);
    }
}
