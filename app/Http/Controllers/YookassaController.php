<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\YooKassaPaymentService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class YookassaController
{
    /**
     * Повторное создание сессии платежа или редирект по активной ссылке
     * Вызывается из ЛК (orders.index) по роуту 'yookassa.pay'
     */
    public function pay(Order $order, YooKassaPaymentService $yookassaService): RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Доступ запрещен.');
        }

        if ($order->status === Order::STATUS_PAID || $order->status === Order::STATUS_CANCELED) {
            return redirect()->route('orders.index')->with('error', 'Этот заказ нельзя оплатить.');
        }

        $lastPayment = $order->payments()
            ->where('provider', 'yookassa')
            ->orderByDesc('created_at')
            ->first();

        if ($lastPayment && in_array($lastPayment->status, ['succeeded', 'waiting_for_capture'])) {
            return redirect()->route('orders.index')->with('info', 'Этот заказ уже находится в процессе оплаты.');
        }

        if ($lastPayment && $lastPayment->status === 'pending' && $lastPayment->confirmation_url) {
            return redirect()->away($lastPayment->confirmation_url);
        }

        $url = $yookassaService->createPaymentForOrder($order);

        if (!$url) {
            return redirect()->route('orders.index')->with('error', 'Не удалось связаться с ЮKassa. Попробуйте позже.');
        }

        return redirect()->away($url);
    }

    /**
     * ИСПРАВЛЕНО: Добавлен метод returnBack для обработки возврата пользователя (return_url)
     */
    public function returnBack(Order $order): Factory|View
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Доступ запрещен.');
        }

        $lastPayment = $order->payments()
            ->where('provider', 'yookassa')
            ->orderByDesc('created_at')
            ->first();

        return view('yookassa.return', [
            'order' => $order,
            'payment' => $lastPayment
        ]);
    }

    /**
     * Асинхронный Webhook от ЮKassa
     * Изменен вызов на делегирование в ваш YookassaPaymentService для обеспечения сквозной GET-проверки
     */
    public function webhook(Request $request, OrderService $orderService, YooKassaPaymentService $yookassaService): JsonResponse
    {
        $payload = $request->all();

        $isProcessed = $yookassaService->handleWebhook($payload, $orderService);

        if (!$isProcessed) {
            return response()->json(['status' => 'ignored_or_not_found'], 200);
        }

        return response()->json(['status' => 'accepted'], 200);
    }
}
