<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\PaymentReceipt;
use App\Models\Product;
use App\Models\User; // Подключаем модель адреса
use Illuminate\Foundation\Testing\DatabaseTransactions; // Меняем на транзакции для безопасности данных
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class YookassaWebhookTest extends TestCase
{
    // Заменяем RefreshDatabase на транзакции, чтобы не тереть ваши товары в основной БД
    use DatabaseTransactions;

    /**
     * Тестирование цепочки: Webhook -> GET-проверка -> Статус платежа -> Статус заказа
     */
    public function test_yookassa_webhook_successfully_pays_order(): void
    {
        // 1. ПОДГОТОВКА ДАННЫХ (Создаем сущности напрямую без фабрик)
        // Создаем пользователя по вашей структуре
        $user = User::create([
            'first_name' => 'Даша',
            'last_name'  => 'Тестовая',
            'email'      => 'dasha-test-' . uniqid() . '@example.com',
            'phone'      => '+79991112233',
            'password'   => bcrypt('secret-password'),
            'status'     => 'active',
        ]);

        // Создаем адрес доставки для пользователя
        $address = Address::create([
            'user_id'    => $user->id,
            'city'       => 'Москва',
            'street'     => 'Ленина',
            'house'      => '10',
            'is_default' => true,
        ]);

        // Создаем товар на складе строго по структуре вашей модели Product
        $product = Product::create([
            'name'        => 'Тестовый товар',
            'description' => 'Описание тестового товара для ЮKassa чека',
            'price'       => 1500.00,
            'stock'       => 10, // Исходный остаток на складе
            'sku'         => 'TEST-SKU-123456', // Обязательный артикул
        ]);

        // Создаем доменный заказ со статусом "pending"
        $order = Order::create([
            'user_id'          => $user->id,
            'address_id'       => $address->id,
            'shipping_address' => 'г. Москва, ул. Ленина, д. 10',
            'status'           => Order::STATUS_PENDING,
            'payment_method'   => Order::PAYMENT_METHOD_YOOKASSA,
            'total'            => 1500.00,
        ]);

        // Привязываем товар к заказу
        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => 2, // Покупатель берет 2 штуки
            'price'      => $product->price,
        ]);

        // Инициализируем локальную платежную сессию (Платёж)
        $externalPaymentId = '27d9aa9a-000f-5000-8000-117a7888b1f5';
        $payment = OrderPayment::create([
            'order_id'            => $order->id,
            'provider'            => 'yookassa',
            'status'              => 'pending',
            'amount'              => $order->total,
            'currency'            => 'RUB',
            'external_payment_id' => $externalPaymentId,
            'idempotence_key'     => 'test-idempotence-uuid-key-' . uniqid(),
        ]);

        // Инициализируем запись локального чека
        $receipt = PaymentReceipt::create([
            'order_payment_id' => $payment->id,
            'type'             => 'sell',
            'status'           => 'pending',
        ]);

        // 2. HTTP MOCKING (Перехватываем внешние запросы к API ЮKassa)
        $baseUrl = rtrim((string) config('services.yookassa.base_url'), '/');

        Http::fake([
            "{$baseUrl}/payments/{$externalPaymentId}" => Http::response([
                'id'                     => $externalPaymentId,
                'status'                 => 'succeeded', // ЮKassa подтверждает успешное списание
                'amount'                 => [
                    'value'    => '1500.00',
                    'currency' => 'RUB',
                ],
                'fiscal_document_number' => '777888999',
                'cancellation_details'   => null,
            ], 200),
        ]);

        // 3. СИМУЛЯЦИЯ ВЕБХУКА (POST-запрос на ваш контроллер)
        $webhookPayload = [
            'type'  => 'notification',
            'event' => 'payment.succeeded',
            'object' => [
                'id'     => $externalPaymentId,
                'status' => 'succeeded',
                'fiscal_document_number' => '777888999',
            ],
        ];

        // Отправляем JSON POST-запрос
        $response = $this->postJson('/payments/yookassa/webhook', $webhookPayload);

        // 4. ПРОВЕРКА РЕЗУЛЬТАТОВ (Assertions)
        $response->assertStatus(200);
        $response->assertJson(['status' => 'accepted']);

        // Проверяем статус платежа в БД
        $payment->refresh();
        $this->assertEquals('succeeded', $payment->status);
        $this->assertNotNull($payment->paid_at);

        // Проверяем статус чека
        $receipt->refresh();
        $this->assertEquals('done', $receipt->status);
        $this->assertEquals('777888999', $receipt->external_receipt_id);

        // Проверяем статус доменного заказа
        $order->refresh();
        $this->assertEquals(Order::STATUS_PAID, $order->status);

        // Проверяем списание остатков на складе через OrderService (10 - 2 = 8)
        $product->refresh();
        $this->assertEquals(8, $product->stock);
    }
}
