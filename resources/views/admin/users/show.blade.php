@extends('layouts.app')

@section('title', 'Просмотр пользователя')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">Карточка пользователя #{{ $user->id }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                ← К списку
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                ✏️ Редактировать
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Левая колонка: Основная информация --}}
        <div class="col-12 col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center py-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3 text-secondary fw-bold h1"
                         style="width: 80px; height: 80px;">
                        {{ mb_substr($user->first_name, 0, 1) }}{{ mb_substr($user->last_name, 0, 1) }}
                    </div>
                    <h5 class="card-title mb-1">{{ $user->first_name }} {{ $user->last_name }}</h5>
                    <span class="badge bg-secondary">{{ $user->roles->first()?->name ?? 'Без роли' }}</span>
                    <div class="d-flex justify-content-center">
                        @if($user->status === 'active')
                            <span class="badge bg-success px-3 py-2 fs-6">Активен</span>
                        @else
                            <span class="badge bg-danger px-3 py-2 fs-6">Заблокирован</span>
                        @endif
                    </div>
                </div>
                <div class="list-group list-group-flush border-top small">
                    <div class="list-group-item px-3 py-2 d-flex justify-content-between">
                        <span class="text-muted">Email:</span>
                        <a href="mailto:{{ $user->email }}" class="text-decoration-none fw-semibold">{{ $user->email }}</a>
                    </div>
                    <div class="list-group-item px-3 py-2 d-flex justify-content-between">
                        <span class="text-muted">Телефон:</span>
                        <span class="fw-semibold">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div class="list-group-item px-3 py-2 d-flex justify-content-between">
                        <span class="text-muted">Зарегистрирован:</span>
                        <span class="fw-semibold">{{ $user->created_at?->format('d.m.Y H:i') ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Адреса доставки --}}
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title h6 mb-0">Адреса доставки ({{ $user->addresses->count() }})</h5>
                </div>
                <div class="list-group list-group-flush small">
                    @forelse($user->addresses as $address)
                        <div class="list-group-item px-3 py-2 d-flex align-items-start justify-content-between">
                            <div>
                                <div>{{ $address->city }}, {{ $address->street }}</div>
                                <div class="text-muted text-xs">{{ $address->postal_code }}</div>
                            </div>
                            {{-- Если в вашей структуре адресов есть признак дефолтного --}}
                            @if($address->is_default)
                                <span class="badge bg-primary bg-opacity-10 text-primary small">Основной</span>
                            @endif
                        </div>
                    @empty
                        <div class="list-group-item text-center text-muted py-3">
                            Адреса не добавлены
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Правая колонка: История заказов --}}
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title h6 mb-0">История заказов</h5>
                    <span class="badge bg-dark">{{ $user->orders->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 100px;">№ Заказа</th>
                            <th scope="col">Дата</th>
                            <th scope="col">Статус</th>
                            <th scope="col" class="text-end">Сумма</th>
                            <th scope="col" class="text-end" style="width: 80px;"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($user->orders as $order)
                            <tr>
                                <td><code>#{{ $order->id }}</code></td>
                                <td>{{ $order->created_at?->format('d.m.Y H:i') ?? '—' }}</td>
                                <td>
                                    {{-- Пример кастомной расцветки статусов (измените под свои) --}}
                                    @php
                                        $statusClass = match($order->status) {
                                            'completed' => 'bg-success',
                                            'pending', 'processing' => 'bg-warning text-dark',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ $order->status_name ?? $order->status }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold">
                                    {{ number_format($order->total_price ?? $order->total ?? 0, 0, ',', ' ') }} ₽
                                </td>
                                <td class="text-end">
                                    {{-- Ссылка на просмотр заказа в админке --}}
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Открыть заказ">
                                        👁️
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Пользователь ещё не совершал покупок.
                                </td >
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

