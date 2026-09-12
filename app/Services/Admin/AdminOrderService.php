<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class AdminOrderService
{
    /**
     * Создание заказа из админки (полностью соответствует вашей логике)
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {

            // 1. Создаем сам ЗАКАЗ со всеми необходимыми полями
            $order = Order::create([
                'user_id'          => $data['user_id'],
                'status'           => $data['status'],         // Например, Order::STATUS_PENDING
                'total'            => $data['total'],          // Сумма передается из запроса
                'payment_method'   => $data['payment_method'], // Способ оплаты
                'address_id'       => $data['address_id'] ?? null,
                'shipping_address' => $data['shipping_address'] ?? null,
            ]);

            // 2. Создаем отдельно СПИСОК ТОВАРОВ (Позиции создаются отдельно, Заказ ≠ Товар)
            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            return $order;
        });
    }
}
