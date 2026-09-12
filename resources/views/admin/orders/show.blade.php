@extends('layouts.app')

@section('title', "Просмотр заказа #{$order->id}")

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-3">
            <h1 class="h3 mb-0">Заказ #{{ $order->id }}</h1>
            @php
                $badgeClass = match($order->status) {
                    \App\Models\Order::STATUS_PENDING => 'bg-warning text-dark',
                    \App\Models\Order::STATUS_PAID => 'bg-info text-white',
                    \App\Models\Order::STATUS_SHIPPED => 'bg-primary',
                    \App\Models\Order::STATUS_COMPLETED => 'bg-success',
                    \App\Models\Order::STATUS_CANCELED => 'bg-danger',
                    default => 'bg-secondary'
                };
            @endphp
            <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">{{ $order->status_label }}</span>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                ← К списку заказов
            </a>
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning">
                ✏️ Изменить статус
            </a>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success mb-3">
            {{ session('status') }}
        </div>
    @endif

    <div class="row g-4">
        {{-- Левая колонка: Содержимое и позиции чека --}}
        <div class="col-12 col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title h6 mb-0">Содержимое заказа</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                        <tr>
                            <th scope="col">Товар</th>
                            <th scope="col" class="text-center" style="width: 120px;">Цена при заказе</th>
                            <th scope="col" class="text-center" style="width: 100px;">Кол-во</th>
                            <th scope="col" class="text-end" style="width: 120px;">Итого</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted small"
                                             style="width: 40px; height: 40px; min-width: 40px;">
                                            📦
                                        </div>
                                        <div>
                                            <div class="fw-semibold">
                                                {{ $item->product?->name ?? 'Товар удален из каталога' }}
                                            </div>
                                            <div class="text-muted text-xs">SKU / Артикул: {{ $item->product?->sku ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-nowrap">
                                    {{ number_format((float) $item->price, 0, ',', ' ') }} ₽
                                </td>
                                <td class="text-center fw-semibold">
                                    {{ $item->quantity }}
                                </td>
                                <td class="text-end fw-semibold text-nowrap">
                                    {{ number_format((float) ($item->price * $item->quantity), 0, ',', ' ') }} ₽
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Итоговая сумма --}}
                <div class="card-footer bg-light py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold text-secondary">Итоговая стоимость:</span>
                        <span class="h4 mb-0 fw-bold text-nowrap">
                        {{ number_format((float) $order->total, 0, ',', ' ') }} ₽
                    </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Правая колонка: Профиль покупателя, оплата и адрес доставки --}}
        <div class="col-12 col-lg-4">
            {{-- Данные клиента --}}
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title h6 mb-0">Покупатель</h5>
                </div>
                <div class="card-body small">
                    @if($order->user)
                        <div class="fw-bold mb-1 fs-6">
                            {{ $order->user->first_name }} {{ $order->user->last_name }}
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('admin.users.show', $order->user) }}" class="text-decoration-none small">
                                👤 Открыть профиль в админке
                            </a>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Email:</span>
                            <a href="mailto:{{ $order->user->email }}" class="text-decoration-none fw-semibold">
                                {{ $order->user->email }}
                            </a>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Телефон:</span>
                            <span class="fw-semibold">{{ $order->user->phone ?? '—' }}</span>
                        </div>
                    @else
                        <div class="text-muted text-center py-2">
                            Заказ оформлен анонимно или аккаунт был удален
                        </div>
                    @endif
                </div>
            </div>

            {{-- Информация по транзакции и логистике --}}
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title h6 mb-0">Детали доставки и оплаты</h5>
                </div>
                <div class="card-body small">
                    <div class="mb-3">
                        <label class="text-muted mb-1 d-block">Метод оплаты:</label>
                        <div class="fw-semibold">{{ $order->payment_method_label }}</div>
                    </div>

                    <hr class="my-3">

                    <div class="mb-3">
                        <label class="text-muted mb-1 d-block">Адрес доставки:</label>
                        <div class="fw-semibold bg-light p-2 rounded border" style="white-space: pre-line;">{{ $order->shipping_address ?? 'Адрес доставки не указан' }}</div>
                    </div>

                    <div class="text-muted text-xs border-top pt-2 mt-3">
                        <div><strong>Создан:</strong> {{ $order->created_at?->format('d.m.Y в H:i') ?? '—' }}</div>
                        <div><strong>Обновлен:</strong> {{ $order->updated_at?->format('d.m.Y в H:i') ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

