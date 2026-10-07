<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\OrderPayment;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderPaymentAdminController
{
    /**
     * Вывод списка всех транзакций ЮKassa
     */
    public function index(Request $request): Factory|View
    {
        $query = OrderPayment::query()
            ->with(['order.user', 'receipt'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('order_id')) {
            $query->where('order_id', $request->query('order_id'));
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    public function show(OrderPayment $payment): Factory|View
    {
        $payment->load(['order.items.product', 'receipt']);

        return view('admin.payments.show', compact('payment'));
    }
}
