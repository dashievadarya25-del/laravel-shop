@extends('layouts.app')

@section('title', 'Мои заказы')

@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-4">Мои заказы</h1>

        @if(session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info shadow-sm">{{ session('info') }}</div>
        @endif

        @if($orders->isEmpty())
            <div class="alert alert-info">У вас пока нет заказов.</div>
        @else
            @foreach($orders as $order)
                @php
                    // Получаем последнюю платежную сессию ЮKassa для этого заказа
                    $lastPayment = $order->payments->first();
                    // Получаем чек, привязанный к этой платежной сессии
                    $receipt = $lastPayment ? $lastPayment->receipt : null;
                @endphp

                <div class="card mb-4 shadow-sm border-0">
                    <!-- Шапка заказа -->
                    <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
                        <div>
                            <span class="fw-bold">Заказ #{{ $order->id }}</span>
                            <span class="text-muted small ms-2">от {{ $order->created_at->format('d.m.Y H:i') }}</span>
                        </div>

                        <!-- Доменный статус заказа -->
                        <span class="badge
                            @if($order->status === \App\Models\Order::STATUS_PAID) text-bg-success
                            @elseif($order->status === \App\Models\Order::STATUS_PENDING) text-bg-warning text-dark
                            @elseif($order->status === \App\Models\Order::STATUS_CANCELED) text-bg-danger
                            @else text-bg-secondary @endif">
                            Магазин: {{ $order->status_label }}
                        </span>
                    </div>

                    <!-- Содержимое заказа -->
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Сумма:</span>
                                <strong>{{ number_format((float) $order->total, 2, '.', ' ') }} ₽</strong>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Способ оплаты:</span>
                                <span>{{ $order->payment_method_label }}</span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block small">Адрес доставки:</span>
                                <span class="text-truncate d-block" style="max-width: 250px;">{{ $order->shipping_address ?? '—' }}</span>
                            </div>
                        </div>

                        <!-- ТЕХНИЧЕСКИЙ БЛОК ИНТЕГРАЦИИ YOOKASSA -->
                        @if($order->payment_method === \App\Models\Order::PAYMENT_METHOD_YOOKASSA)
                            <div class="p-3 bg-light rounded border mb-3 small">
                                <div class="row align-items-center">
                                    <!-- 1. Статус последнего платежа -->
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <div class="mb-1">
                                            <strong>Статус платежа:</strong>
                                            @if($lastPayment)
                                                <span class="badge
                                                    @if($lastPayment->status === 'succeeded') text-bg-success
                                                    @elseif($lastPayment->status === 'pending') text-bg-info text-white
                                                    @elseif($lastPayment->status === 'waiting_for_capture') text-bg-primary
                                                    @else text-bg-danger @endif">
                                                    {{ $lastPayment->status }}
                                                </span>
                                            @else
                                                <span class="text-muted">Не инициализирован</span>
                                            @endif
                                        </div>

                                        <!-- 4. Статус чека (ФЗ-54) -->
                                        <div>
                                            <strong>Статус чека:</strong>
                                            @if($receipt)
                                                <span class="badge
                                                    @if($receipt->status === 'done') text-bg-success
                                                    @elseif($receipt->status === 'pending') text-bg-warning text-dark
                                                    @else text-bg-danger @endif">
                                                    {{ $receipt->status }}
                                                </span>
                                                @if($receipt->external_receipt_id)
                                                    <span class="text-muted text-xs ms-1">(№ ФД: {{ $receipt->external_receipt_id }})</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Не формировался</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- ИНТЕРФЕЙСНЫЕ КНОПКИ ДЛЯ УПРАВЛЕНИЯ ССЫЛКАМИ -->
                                    <div class="col-md-6 text-md-end d-flex flex-wrap gap-2 justify-content-md-end">
                                        @if($order->status === \App\Models\Order::STATUS_PENDING)

                                            <!-- 2. Кнопка "Перейти к оплате": Активна, если сессия pending и есть живая ссылка -->
                                            @if($lastPayment && $lastPayment->status === 'pending' && $lastPayment->confirmation_url)
                                                <a href="{{ route('yookassa.pay', $order) }}" class="btn btn-primary btn-sm fw-bold">
                                                    💳 Перейти к оплате
                                                </a>
                                            @endif

                                            <!-- 3. Кнопка "Сформировать новую ссылку": Доступна, если прошлый платеж отменен или ссылок вообще не было -->
                                            @if(!$lastPayment || $lastPayment->status === 'canceled')
                                                <a href="{{ route('yookassa.pay', $order) }}" class="btn btn-outline-success btn-sm fw-bold">
                                                    🔄 Сформировать новую ссылку
                                                </a>
                                            @endif

                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Список купленных товаров -->
                        <h6 class="fs-7 text-muted mb-2">Состав заказа:</h6>
                        <ul class="list-group list-group-flush border rounded mb-3" style="max-height: 150px; overflow-y: auto;">
                            @foreach($order->items as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2 bg-transparent small">
                                    <div class="text-truncate" style="max-width: 80%;">
                                        {{ $item->product?->name ?? 'Товар удален' }}
                                        <span class="text-muted ms-1">× {{ $item->quantity }}</span>
                                    </div>
                                    <span class="fw-semibold text-nowrap">{{ number_format((float) $item->price, 0, ',', ' ') }} ₽</span>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Кнопка отмены заказа магазином -->
                        @if($order->status === \App\Models\Order::STATUS_PENDING)
                            <form method="POST" action="{{ route('orders.status.update', $order) }}" class="mb-0 text-end">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ \App\Models\Order::STATUS_CANCELED }}">
                                <button type="submit" class="btn btn-link text-danger p-0 btn-sm text-decoration-none" onclick="return confirm('Отменить данный заказ?')">
                                    ✕ Отменить заказ в магазине
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
