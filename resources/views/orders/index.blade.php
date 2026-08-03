@extends('layouts.app')

@section('title', 'Мои заказы')

@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-3">Мои заказы</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        @if($orders->isEmpty())
            <div class="alert alert-info">
                У вас пока нет заказов.
            </div>
        @else
            @foreach($orders as $order)
                <div class="card mb-3">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            Заказ #{{ $order->id }}
                            <span class="text-muted">от {{ $order->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <span class="badge text-bg-secondary">{{ $order->status_label }}</span>
                    </div>

                    <div class="card-body">
                        <div class="mb-2">
                            <span class="text-muted">Сумма:</span>
                            <strong>{{ number_format((float) $order->total, 0, ',', ' ') }} ₽</strong>
                        </div>

                        <div class="mb-2">
                            <span class="text-muted">Оплата:</span>
                            {{ $order->payment_method_label }}
                        </div>

                        <div class="mb-3">
                            <span class="text-muted">Адрес:</span>
                            {{ $order->shipping_address ?? '—' }}
                        </div>

                        <ul class="list-group list-group-flush mb-3">
                            @foreach($order->items as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        {{ $item->product?->name ?? 'Товар удален' }}
                                        <span class="text-muted">× {{ $item->quantity }}</span>
                                    </div>
                                    <div class="fw-semibold">
                                        {{ number_format((float) $item->price, 0, ',', ' ') }} ₽
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        @if($order->status === \App\Models\Order::STATUS_PENDING)
                            <div class="d-flex gap-2">
                                <form method="POST" action="{{ route('orders.status.update', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ \App\Models\Order::STATUS_CANCELED }}">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Отменить</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
