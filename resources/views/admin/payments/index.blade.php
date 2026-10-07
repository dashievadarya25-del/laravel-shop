@extends('layouts.app')

@section('title', 'Админ-панель: Платежи ЮKassa')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Логи платежей ЮKassa</h1>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">← В админку</a>
        </div>

        <!-- Блок фильтрации -->
        <div class="card mb-4 border-0 shadow-sm bg-light">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">ID заказа</label>
                        <input type="number" name="order_id" value="{{ request('order_id') }}" class="form-control form-control-sm" placeholder="Например: 18">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Статус платежа</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Все статусы</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>pending</option>
                            <option value="waiting_for_capture" {{ request('status') === 'waiting_for_capture' ? 'selected' : '' }}>waiting_for_capture</option>
                            <option value="succeeded" {{ request('status') === 'succeeded' ? 'selected' : '' }}>succeeded</option>
                            <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>canceled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Применить</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm w-100">Сбросить</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Таблица транзакций -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0 small">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>ID Заказа</th>
                            <th>Покупатель</th>
                            <th>Сумма</th>
                            <th>ID ЮKassa (External ID)</th>
                            <th>Статус транзакции</th>
                            <th>Статус чека</th>
                            <th>Дата создания</th>
                            <th class="text-end">Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="fw-bold">{{ $payment->id }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="text-decoration-none fw-semibold">
                                        #{{ $payment->order_id }}
                                    </a>
                                </td>
                                <td>
                                    @if($payment->order && $payment->order->user)
                                        {{ $payment->order->user->first_name }} {{ $payment->order->user->last_name }}
                                        <span class="text-muted d-block text-xs">{{ $payment->order->user->email }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-nowrap">
                                    {{ number_format((float) $payment->amount, 2, '.', ' ') }} {{ $payment->currency }}
                                </td>
                                <td class="text-muted text-monospace" style="font-size: 0.8rem;">
                                    {{ $payment->external_payment_id ?? 'Не присвоен (Ошибка API)' }}
                                </td>
                                <td>
                                    <span class="badge
                                        @if($payment->status === 'succeeded') text-bg-success
                                        @elseif($payment->status === 'pending') text-bg-info text-white
                                        @elseif($payment->status === 'waiting_for_capture') text-bg-primary
                                        @else text-bg-danger @endif">
                                        {{ $payment->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($payment->receipt)
                                        <span class="badge
                                            @if($payment->receipt->status === 'done') text-bg-success
                                            @elseif($payment->receipt->status === 'pending') text-bg-warning text-dark
                                            @else text-bg-danger @endif">
                                            Фискализация: {{ $payment->receipt->status }}
                                        </span>
                                    @else
                                        <span class="text-muted">Без чека</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $payment->created_at->format('d.m.Y H:i:s') }}</td>
                                <td class="text-end px-3">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-outline-primary btn-xs py-1 px-2 fw-semibold">
                                        🔍 Тех. инфо
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Платежи по заданным фильтрам не найдены.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Блок пагинации -->
            @if($payments->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
