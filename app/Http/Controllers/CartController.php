<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTOs\CartItemsDto;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Product;
use App\Services\SessionCartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController
{
    public function __construct(
        private SessionCartService $sessionCartService,
    ) {
    }

    public function index(): Factory|View
    {
        return view('cart.index', $this->getCartData());
    }

    public function store(Product $product, StoreCartItemRequest $request): JsonResponse|RedirectResponse
    {
        $dto = CartItemsDto::fromRequest($request);
        $this->sessionCartService->add($product, $dto);

        return $this->respond($request);
    }

    public function update(Product $product, UpdateCartItemRequest $request): JsonResponse|RedirectResponse
    {
        $dto = CartItemsDto::fromRequest($request);
        $this->sessionCartService->setQuantity($product, $dto);

        return $this->respond($request);
    }

    public function destroy(Product $product, Request $request): JsonResponse|RedirectResponse
    {
        $this->sessionCartService->remove($product);

        if ($this->sessionCartService->getTotalQuantity() === 0) {
            return $this->respondWithRedirect($request);
        }

        return $this->respond($request);
    }

    public function clear(Request $request): JsonResponse|RedirectResponse
    {
        $this->sessionCartService->clear();
        return $this->respondWithRedirect($request);
    }

    /**
     * Формирует единый массив данных для обычного рендера и AJAX
     */
    private function getCartData(): array
    {
        return [
            'items'          => $this->sessionCartService->getItems(),
            'totalQuantity'  => $this->sessionCartService->getTotalQuantity(),
            'totalPrice'     => $this->sessionCartService->getTotalPrice(),
            'defaultAddress' => Auth::user()?->addresses()->where('is_default', true)->first(),
        ];
    }

    private function respond(Request $request): JsonResponse|RedirectResponse
    {
        $totalQuantity = $this->sessionCartService->getTotalQuantity();

        if ($request->expectsJson()) {
            return response()->json([
                'cartCount' => $totalQuantity,
                'html'      => view('cart._content', $this->getCartData())->render(),
            ]);
        }

        return redirect()
            ->back()
            ->with('cartCount', $totalQuantity);
    }

    private function respondWithRedirect(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'cartCount' => 0,
                'redirect'  => route('categories.index'),
            ]);
        }

        return redirect()->route('categories.index');
    }
}
