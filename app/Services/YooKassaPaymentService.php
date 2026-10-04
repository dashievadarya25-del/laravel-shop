<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PaymentReceipt;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class YooKassaPaymentService
{
    protected string $shopId;
    protected string $secretKey;
    protected string $baseUrl;
    protected string $currency;
    protected int $timeout;
    protected array $receiptConfig;

    public function __construct()
    {
        $this->shopId = (string) config('services.yookassa.shop_id');
        $this->secretKey = (string) config('services.yookassa.secret_key');
        $this->baseUrl = rtrim((string) config('services.yookassa.base_url'), '/');
        $this->currency = (string) config('services.yookassa.currency', 'RUB');
        $this->timeout = (int) config('services.yookassa.timeout', 15);
        $this->receiptConfig = (array) config('services.yookassa.receipts', []);
    }

    /**
     * 1. Создание платежа в YooKassa и локальной сессии order_payments
     */
    public function createPaymentForOrder(Order $order): ?string
    {
        $idempotenceKey = (string) Str::uuid();
        $apiUrl = "{$this->baseUrl}/payments";

        $requestBody = [
            'amount' => [
                'value' => number_format((float) $order->total, 2, '.', ''),
                'currency' => $this->currency,
            ],
            'capture' => true,
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => route('payments.yookassa.return', $order),
            ],
            'description' => 'Оплата заказа #' . $order->id,
            'metadata' => [
                'order_id' => $order->id,
            ],
        ];

        if ($this->receiptConfig['enabled'] ?? true) {
            $requestBody['receipt'] = $this->buildReceiptPayload($order);
        }

        $response = Http::withBasicAuth($this->shopId, $this->secretKey)
            ->timeout($this->timeout)
            ->withHeaders(['Idempotence-Key' => $idempotenceKey])
            ->post($apiUrl, $requestBody);

        $responseData = $response->json();

        if ($response->failed()) {
            logger()->error('YooKassa Payment API Failure', ['order_id' => $order->id, 'response' => $responseData]);

            OrderPayment::create([
                'order_id' => $order->id,
                'provider' => 'yookassa',
                'status' => 'canceled',
                'amount' => $order->total,
                'currency' => $this->currency,
                'idempotence_key' => $idempotenceKey,
                'request_payload' => $requestBody,
                'response_payload' => $responseData,
                'error_message' => $responseData['description'] ?? 'API Error'
            ]);

            return null;
        }

        $payment = OrderPayment::create([
            'order_id' => $order->id,
            'provider' => 'yookassa',
            'status' => 'pending',
            'amount' => $order->total,
            'currency' => $this->currency,
            'external_payment_id' => $responseData['id'],
            'idempotence_key' => $idempotenceKey,
            'confirmation_url' => $responseData['confirmation']['confirmation_url'] ?? null,
            'request_payload' => $requestBody,
            'response_payload' => $responseData
        ]);

        if (isset($requestBody['receipt'])) {
            $this->createReceipt($payment, $requestBody['receipt']);
        }

        return $responseData['confirmation']['confirmation_url'] ?? null;
    }

    /**
     * 2. Обработка Webhook от YooKassa
     */
    public function handleWebhook(array $payload, OrderService $orderService): bool
    {
        $event = $payload['event'] ?? null;
        $object = $payload['object'] ?? null;

        if (!$object || !isset($object['id'])) {
            return false;
        }

        $payment = OrderPayment::where('external_payment_id', $object['id'])
            ->where('provider', 'yookassa')
            ->first();

        if (!$payment) {
            return false;
        }

        return $this->synchronizePayment($payment, $object, $event, $orderService);
    }

    /**
     * 3. Прямой запрос в API YooKassa для получения актуального статуса платежа
     */
    public function fetchPayment(string $externalPaymentId): ?array
    {
        $apiUrl = "{$this->baseUrl}/payments/{$externalPaymentId}";

        $response = Http::withBasicAuth($this->shopId, $this->secretKey)
            ->timeout($this->timeout)
            ->get($apiUrl);

        return $response->successful() ? $response->json() : null;
    }

    /**
     * 4. Синхронизация локальных статусов на основе переданного состояния от YooKassa
     * Используется как внутри Webhook, так и при ручном или фоновом обновлении (Polling).
     */
    public function synchronizePayment(OrderPayment $payment, array $yookassaObject, ?string $event = null, ?OrderService $orderService = null): bool
    {
        $status = $yookassaObject['status'] ?? null;

        $updateData = [
            'status' => $status,
            'response_payload' => $yookassaObject
        ];

        if ($status === 'succeeded') {
            $updateData['paid_at'] = Carbon::now();
            $payment->update($updateData);

            $payment->receipt()->update([
                'status' => 'done',
                'external_receipt_id' => $yookassaObject['fiscal_document_number'] ?? null,
                'response_payload' => $yookassaObject
            ]);

            if ($orderService) {
                $orderService->markAsPaid($payment->order);
            }
            return true;
        }

        if ($status === 'canceled') {
            $updateData['canceled_at'] = Carbon::now();
            $updateData['error_message'] = $yookassaObject['cancellation_details']['reason'] ?? 'Canceled';
            $payment->update($updateData);

            $payment->receipt()->update([
                'status' => 'failed',
                'error_message' => $yookassaObject['cancellation_details']['reason'] ?? 'Payment canceled',
                'response_payload' => $yookassaObject
            ]);
            return true;
        }

        $payment->update($updateData);
        return true;
    }

    /**
     * 5. Создание локальной записи чека в таблице payment_receipts
     */
    public function createReceipt(OrderPayment $payment, array $receiptPayload): PaymentReceipt
    {
        return PaymentReceipt::create([
            'order_payment_id' => $payment->id,
            'external_receipt_id' => null,
            'type' => 'sell', // Приход
            'status' => 'pending',
            'send_to_customer' => true,
            'request_payload' => $receiptPayload,
            'response_payload' => null
        ]);
    }

    /**
     * Вспомогательный метод: Сборка состава чека по ФЗ-54 под ваши .env параметры
     */
    protected function buildReceiptPayload(Order $order): array
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'description' => Str::limit($item->name ?? "Товар ID {$item->product_id}", 128),
                'quantity' => (string) $item->quantity,
                'amount' => [
                    'value' => number_format((float) $item->price, 2, '.', ''),
                    'currency' => $this->currency
                ],
                'vat_code' => (int) ($this->receiptConfig['vat_code'] ?? 1),
                'payment_mode' => (string) ($this->receiptConfig['payment_mode'] ?? 'full_payment'),
                'payment_subject' => (string) ($this->receiptConfig['payment_subject'] ?? 'commodity')
            ];
        }

        return [
            'customer' => [
                'email' => $order->user->email ?? 'customer@example.com',
            ],
            'items' => $items,
            'tax_system_code' => (int) ($this->receiptConfig['tax_system_code'] ?? 1)
        ];
    }
}
