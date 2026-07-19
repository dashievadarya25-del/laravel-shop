<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CartItemsDto;
use App\Models\Product;

class SessionCartService
{
    private const string SESSION_KEY = 'cart.items';

    private function getRaw(): array
    {
        $raw = session()->get(self::SESSION_KEY, []);

        if (!is_array($raw)) {
            return [];
        }

        $result = [];
        foreach ($raw as $productId => $quantity) {
            $productId = (int) $productId;
            $quantity = (int) $quantity;

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $result[$productId] = $quantity;
        }

        return $result;
    }

    private function putRaw(array $raw): void
    {
        // Убрали безопасный оператор ?-> так как хелпер session() в Laravel всегда возвращает объект
        session()->put(self::SESSION_KEY, $raw);
    }

    private function removeById(int $productId): void
    {
        $raw = $this->getRaw();
        unset($raw[$productId]);
        $this->putRaw($raw);
    }

    public function getItems(): array
    {
        $raw = $this->getRaw();
        $productIds = array_map('intval', array_keys($raw));

        if (empty($productIds)) {
            return [];
        }

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $items = [];
        $hasMissingProducts = false;

        foreach ($raw as $productId => $quantity) {
            $product = $products->get((int) $productId);

            // Если товара нет в БД, помечаем, что нужно почистить сессию
            if (!$product) {
                unset($raw[$productId]);
                $hasMissingProducts = true;
                continue;
            }

            $quantity = max(1, (int) $quantity);
            $unitPrice = (string) $product->price;
            $subtotal = bcmul($unitPrice, (string) $quantity, 2);

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
        }

        // Синхронизируем сессию один раз, если были удаленные товары
        if ($hasMissingProducts) {
            $this->putRaw($raw);
        }

        return $items;
    }

    public function getTotalQuantity(): int
    {
        return (int) collect($this->getRaw())->sum();
    }

    public function getTotalPrice(): string
    {
        $total = '0.00';
        foreach ($this->getItems() as $item) {
            $total = bcadd($total, $item['subtotal'], 2);
        }

        return $total;
    }

    /**
     * Принимает DTO для добавления товара
     */
    public function add(Product $product, CartItemsDto $dto): int
    {
        $quantity = max(1, $dto->quantity);

        $raw = $this->getRaw();
        $current = (int) ($raw[$product->id] ?? 0);
        $next = $current + $quantity;

        if ($product->stock <= 0) {
            $this->removeById($product->id);
            return 0;
        }

        $next = min($next, $product->stock);
        $raw[$product->id] = $next;

        $this->putRaw($raw);

        return $next;
    }

    /**
     * Принимает DTO для обновления количества
     */
    public function setQuantity(Product $product, CartItemsDto $dto): int
    {
        $quantity = max(0, $dto->quantity);

        if ($product->stock <= 0 || $quantity === 0) {
            $this->removeById($product->id);
            return 0;
        }

        $quantity = min($quantity, $product->stock);

        $raw = $this->getRaw();
        $raw[$product->id] = $quantity;
        $this->putRaw($raw);

        return $quantity;
    }

    public function remove(Product $product): void
    {
        $this->removeById($product->id);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}
