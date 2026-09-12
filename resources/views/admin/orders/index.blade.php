@php
   $search = request('search', '');
   $statusFilter = request('status', '');
@endphp

@extends('layouts.app')

@section('title', 'Управление заказами')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Управление заказами</h1>
        <a href="{{ route('admin.orders.create') }}" class="btn btn-success">
            ➕ Создать заказ
        </a>
    </div>

    {{-- Всплывающие уведомления об успешных операциях --}}
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    {{-- Общий блок ошибок --}}
    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Блок фильтрации и поиска --}}
    <form method="GET" action="{{ route('admin.orders.index') }}" class="card card-body mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-6 col-lg-6">
                <label for="search" class="form-label mb-1">Поиск</label>
                <input type="text"
                       name="search"
                       id="search"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="ID заказа, Email или фамилия клиента">
            </div>

            <div class="col-12 col-md-4 col-lg-4">
                <label for="status" class="form-label mb-1">Статус заказа</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Все статусы</option>
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected($statusFilter === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-2 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Применить</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Сбросить</a>
            </div>
        </div>
    </form>

    {{-- Таблица списка заказов --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th scope="col" style="width: 90px;">ID</th>
                    <th scope="col">Покупатель</th>
                    <th scope="col">Дата оформления</th>
                    <th scope="col">Статус</th>
                    <th scope="col" class="text-end" style="width: 150px;">Сумма</th>
                    <th scope="col" class="text-end" style="width: 150px;">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><code>#{{ $order->id }}</code></td>
                        <td>
                            @if($order->user)
                                <div class="fw-semibold">{{ $order->user->first_name }} {{ $order->user->last_name }}</div>
                                <div class="text-muted small">{{ $order->user->email }}</div>
                            @else
                                <span class="text-muted small">Пользователь удален / Гость</span>
                            @endif
                        </td>
                        <td>
                            {{ $order->created_at?->format('d.m.Y H:i') ?? '—' }}
                        </td>
                        <td>
                            {{-- Динамический цвет бэйджа на основе ваших констант из модели Order --}}
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
                            <span class="badge {{ $badgeClass }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="text-end fw-semibold text-nowrap">
                            {{ number_format((float)$order->total, 0, ',', ' ') }} ₽
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                {{-- Просмотр --}}
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-info" title="Просмотр">
                                    👁️
                                </a>
                                {{-- Изменение статуса --}}
                                <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-warning" title="Изменить статус">
                                    ✏️
                                </a>
                                {{-- Удаление --}}
                                <form action="{{ route('admin.orders.destroy', $order) }}"
                                      method="POST"
                                      class="d-inline mb-0"
                                      onsubmit="return confirm('Вы уверены, что хотите полностью удалить заказ #{{ $order->id }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Удалить">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            Заказы не найдены.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Пагинация страниц --}}
        @if($orders->hasPages())
            <div class="card-footer bg-white pt-3">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
