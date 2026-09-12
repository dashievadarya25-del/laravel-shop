<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderStoreRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderAdminController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {
    }

    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('email', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();
        $statusLabels = Order::STATUS_LABELS;

        return view('admin.orders.index', compact('orders', 'statusLabels'));
    }

    public function create(): View
    {
        $users = User::all();
        $products = Product::where('stock', '>', 0)->get();
        $statusLabels = Order::STATUS_LABELS;
        $paymentLabels = Order::PAYMENT_METHOD_LABELS;

        return view('admin.orders.create', compact('users', 'products', 'statusLabels', 'paymentLabels'));
    }

    public function store(OrderStoreRequest $request, OrderService $service): RedirectResponse
    {
        try {
            $service->create($request->validated());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('status', 'Заказ успешно создан');
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $statusLabels = Order::STATUS_LABELS;

        return view('admin.orders.edit', compact('order', 'statusLabels'));
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->orderService->updateStatusFromAdmin($order, $request->input('status'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('status', "Статус заказа #{$order->id} успешно изменен");
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->items()->delete();
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('status', 'Заказ успешно удален');
    }
}
