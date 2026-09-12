@extends('layouts.app')

@section('title', "Изменение статуса заказа #{$order->id}")

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Изменение статуса заказа #{{ $order->id }}</h1>
        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-secondary">
            ← Назад к просмотру
        </a>
    </div>

    {{-- Вывод критических ошибок (например, если при переводе в Paid не хватило товара на складе) --}}
    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Левая колонка: Форма переключения статуса --}}
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title h6 mb-0">Управление заказом</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="status" class="form-label mb-1">Выберите новый статус <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach($statusLabels as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $order->status) === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning small py-2 mb-4">
                            <i class="bi bi-info-circle"></i> При переводе заказа в статус <strong>«Оплачен»</strong> система выполнит транзакцию и автоматически уменьшит остатки товаров на складе.
                        </div>

                        <div class="d-grid border-top pt-3">
                            <button type="submit" class="btn btn-primary">Обновить статус</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Правая колонка: Сводная информация по чеку --}}
        <div class="col-12 col-md-6 col-lg-8">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-light py-3">
                    <h5 class="card-title h6 mb-0">Контекст и состав заказа</h5>
                </div>
                <div class="card-body small">
                    <div class="row g-3">
                        <div class="col-6">
                            <span class="text-muted d-block small">Покупатель:</span>
                            <span class="fw-semibold">
                            @if($order->user)
                                    {{ $order->user->first_name }} {{ $order->user->last_name }}
                                @else
                                    <span class="text-muted">Гость / Удален</span>
                                @endif
                        </span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block small">Способ оплаты:</span>
                            <span class="fw-semibold">{{ $order->payment_method_label }}</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block small">Адрес доставки:</span>
                            <div class="p-2 bg-light border rounded mt-1 fw-semibold text-wrap" style="white-space: pre-line;">
                                {{ $order->shipping_address ?? 'Адрес не указан' }}
                            </div>
                        </div>
                        <div class="col-12">
                            <span class="text-muted d-block mb-1 small">Позиции в заказе ({{ $order->items->count() }}):</span>
                            <ul class="list-group list-group-flush border rounded overflow-hidden">
                                @foreach($order->items as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2 bg-light bg-opacity-25">
                                    <span>
                                        {{ $item->product?->name ?? 'Товар удален' }}
                                        <small class="text-muted text-nowrap">x{{ $item->quantity }}</small>
                                    </span>
                                        <span class="text-nowrap fw-semibold">
                                        {{ number_format((float)($item->price * $item->quantity), 0, ',', ' ') }} ₽
                                    </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-12 text-end border-top pt-2">
                            <span class="text-muted">Итоговая сумма:</span>
                            <span class="h5 mb-0 fw-bold d-block text-primary mt-1">
                            {{ number_format((float) $order->total, 0, ',', ' ') }} ₽
                        </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

