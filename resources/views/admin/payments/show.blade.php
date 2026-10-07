@extends('layouts.app')

@section('title', 'Тех. данные платежа #' . $payment->id)

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4">Технический аудит платежной сессии #{{ $payment->id }}</h1>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary btn-sm">← К списку платежей</a>
        </div>

        <div class="row g-4">
            <!-- Левая колонка: Основные метрики -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-dark text-white fw-bold small">Параметры сессии</div>
                    <div class="card-body small">
                        <p class="mb-2"><strong>Провайдер:</strong> {{ $payment->provider }}</p>
                        <p class="mb-2"><strong>Сумма транзакции:</strong> {{ number_format((float) $payment->amount, 2, '.', ' ') }} {{ $payment->currency }}</p>
                        <p class="mb-2"><strong>Локальный статус:</strong> <span class="badge bg-secondary">{{ $payment->status }}</span></p>
                        <p class="mb-2"><strong>Ключ идемпотентности:</strong> <br><span class="text-muted text-monospace text-xs">{{ $payment->idempotence_key }}</span></p>
                        @if($payment->error_message)
                            <div class="alert alert-danger p-2 mb-0 mt-2 small">
                                <strong>Текст ошибки шлюза:</strong><br>{{ $payment->error_message }}
                            </div>
                        @endif
                    </div>
                </div>

                @if($payment->receipt)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-dark text-white fw-bold small">Данные фискализации (Чек)</div>
                        <div class="card-body small">
                            <p class="mb-2"><strong>ID Чека в БД:</strong> {{ $payment->receipt->id }}</p>
                            <p class="mb-2"><strong>Тип операции:</strong> {{ $payment->receipt->type }}</p>
                            <p class="mb-2"><strong>Статус чека:</strong> <span class="badge bg-info text-white">{{ $payment->receipt->status }}</span></p>
                            <p class="mb-2"><strong>Флаг отправки клиенту:</strong> {{ $payment->receipt->send_to_customer ? 'Да' : 'Нет' }}</p>
                            <p class="mb-0"><strong>№ Фискального документа (ФД):</strong> <br><span class="text-primary fw-bold">{{ $payment->receipt->external_receipt_id ?? 'Не присвоен' }}</span></p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Правая колонка: Логи JSON payloads -->
            <div class="col-md-8">
                <!-- Сырой запрос к YooKassa -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-secondary text-white fw-bold small">Request Payload (Данные, отправленные в ЮKassa)</div>
                    <div class="card-body p-0">
                        <pre class="bg-dark text-light p-3 rounded-bottom mb-0 style-scroll" style="max-height: 250px; overflow-y: auto; font-size: 0.75rem;"><code>{{ json_encode($payment->request_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                </div>

                <!-- Сырой ответ / Вебхук от YooKassa -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white fw-bold small">Response Payload (Последний ответ / Webhook от ЮKassa)</div>
                    <div class="card-body p-0">
                        <pre class="bg-dark text-light p-3 rounded-bottom mb-0 style-scroll" style="max-height: 350px; overflow-y: auto; font-size: 0.75rem;"><code>{{ json_encode($payment->response_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .text-xs { font-size: 0.75rem; }
        .style-scroll::-webkit-scrollbar { width: 5px; }
        .style-scroll::-webkit-scrollbar-thumb { background: #6c757d; border-radius: 4px; }
    </style>
@endsection
