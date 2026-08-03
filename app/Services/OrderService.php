<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function createOrder(User $user, string $paymentMethod, SessionCartService $cart): Order
    {
        $items = $cart->getItems();
        if (empty($items)) {
            throw ValidationException::withMessages([
                'cart' => 'Корзина пуста.',
            ]);
        }

        $address = $user->addresses()
            ->where('is_default', true)
            ->first();

        if (!$address) {
            throw ValidationException::withMessages([
                'address' => 'Добавьте адрес в профиле и выберите основной.',
            ]);
        }

        return DB::transaction(function () use ($user, $address, $paymentMethod, $cart, $items): Order {
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'shipping_address' => $address->full_address,
                'status' => Order::STATUS_PENDING,
                'payment_method' => $paymentMethod,
                'total' => $cart->getTotalPrice(),
            ]);

            foreach ($items as $item) {
                $product = $item['product'] ?? $item->product ?? $item;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            $cart->clear();

            return $order;
        });
    }

    public function markAsPaid(Order $order): void
    {
        if ($order->status !== Order::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Нельзя оплатить заказ в текущем статусе.',
            ]);
        }

        DB::transaction(function () use ($order): void {
            $items = $order->items()->get();
            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'status' => 'В заказе нет товаров.',
                ]);
            }

            $products = Product::query()
                ->whereIn('id', $items->pluck('product_id')->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $product = $products->get($item->product_id);
                if (!$product || $product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'status' => "Недостаточно товара на складе для позиции ID: {$item->product_id}.",
                    ]);
                }
            }

            foreach ($items as $item) {
                $product = $products->get($item->product_id);
                $product->stock = $product->stock - $item->quantity;
                $product->save();
            }

            $order->status = Order::STATUS_PAID;
            $order->save();
        });
    }

    public function cancel(Order $order): void
    {
        if ($order->status !== Order::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'status' => 'Нельзя отменить заказ в текущем статусе.',
            ]);
        }

        $order->status = Order::STATUS_CANCELED;
        $order->save();
    }
}
