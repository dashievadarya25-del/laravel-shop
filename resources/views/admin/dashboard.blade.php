@extends('layouts.app')

@section('title', 'Панель управления интернет-магазином')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Сводный дашборд (Последние 7 дней)</h1>
            <span class="text-muted small">
            Данные с {{ \Illuminate\Support\Carbon::now()->subDays(6)->format('d.m') }} по {{ \Illuminate\Support\Carbon::now()->format('d.m') }}
        </span>
        </div>

        <!-- КАРТОЧКИ КЛЮЧЕВЫХ KPI ЧЕРЕЗ МАССИВ $REPORT -->
        <div class="row g-3 mb-4">
            <!-- 1. Всего заказов -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-bg-light p-3">
                    <div class="text-muted small text-uppercase fw-semibold mb-1">Всего заказов</div>
                    <div class="fs-2 fw-bold text-dark">{{ $report['ordersCount'] }}</div>
                </div>
            </div>

            <!-- 2. Продаж -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-bg-success p-3">
                    <div class="text-white-50 small text-uppercase fw-semibold mb-1">Продаж</div>
                    <div class="fs-2 fw-bold text-white">{{ $report['salesCount'] }}</div>
                </div>
            </div>

            <!-- 3. Выручка -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-bg-primary p-3">
                    <div class="text-white-50 small text-uppercase fw-semibold mb-1">Выручка</div>
                    <div class="fs-2 fw-bold text-white">{{ number_format($report['revenue'], 2, ',', ' ') }} ₽</div>
                </div>
            </div>

            <!-- 4. Отменено -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm text-bg-danger p-3">
                    <div class="text-white-50 small text-uppercase fw-semibold mb-1">Отменено заказов</div>
                    <div class="fs-2 fw-bold text-white">{{ $report['canceledCount'] }}</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- ТАБЛИЦА ПРОДАЖ ПО КАЖДОМУ ДНЮ -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white fw-bold py-3 small">
                        📊 Таблица продаж по каждому дню
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0 small">
                                <thead class="table-light">
                                <tr>
                                    <th class="px-4" style="width: 30%;">Дата</th>
                                    <th class="text-center" style="width: 35%;">Продаж</th>
                                    <th class="text-end px-4" style="width: 35%;">Выручка</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($report['dailySales'] as $day)
                                    <tr @if($day['date'] === \Illuminate\Support\Carbon::now()->format('d.m')) class="table-info fw-semibold" @endif>
                                        <!-- Дата в формате 01.07 -->
                                        <td class="px-4">{{ $day['date'] }}</td>

                                        <!-- Продаж за день -->
                                        <td class="text-center">
                                            <span class="badge {{ $day['salesCount'] > 0 ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $day['salesCount'] }}
                                            </span>
                                        </td>

                                        <!-- Выручка за день -->
                                        <td class="text-end px-4 fw-bold {{ $day['revenue'] > 0 ? 'text-primary' : 'text-muted' }}">
                                            {{ number_format((float) $day['revenue'], 0, ',', ' ') }} ₽
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- НАВИГАЦИЯ АДМИНКИ -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white fw-bold py-3 small">
                        🛠 Инструменты администрирования
                    </div>
                    <div class="card-body p-2">
                        <div class="list-group list-group-flush small">
                            <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center">
                                <span>🛒 Управление заказами</span>
                                <span class="badge bg-secondary rounded-pill">CRUD</span>
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center">
                                <span>📦 Управление товарами</span>
                                <span class="badge bg-secondary rounded-pill">CRUD</span>
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center">
                                <span>👥 Управление пользователями</span>
                                <span class="badge bg-secondary rounded-pill">CRUD</span>
                            </a>
                            <a href="{{ route('admin.payments.index') }}" class="list-group-item list-group-item-action py-3 d-flex justify-content-between align-items-center bg-light fw-bold text-primary">
                                <span>💳 Логи платежей ЮKassa</span>
                                <span class="badge bg-primary text-white rounded-pill">Logs</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
