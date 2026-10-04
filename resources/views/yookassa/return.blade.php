@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">

            <!-- Состояние 1: Ожидание / Обработка платежа -->
            <div id="status-processing" class="card p-5 shadow-sm border-0">
                <div class="spinner-border text-primary mb-4" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
                <h2 class="h4 mb-2">Проверяем статус платежа...</h2>
                <p class="text-muted small">Обычно это занимает не больше нескольких секунд. Пожалуйста, не закрывайте страницу.</p>
                <div class="p-3 bg-light rounded border text-start small mb-0">
                    <strong>Заказ:</strong> #{{ $order->id }}<br>
                    <strong>Сумма к оплате:</strong> {{ number_format((float) $order->total, 2, '.', ' ') }} ₽
                </div>
            </div>

            <!-- Состояние 2: Успешная оплата (Изначально скрыто) -->
            <div id="status-success" class="card p-5 shadow-sm border-0 d-none">
                <div class="mb-4 text-success" style="font-size: 3.5rem;">🎉</div>
                <h2 class="h4 mb-2 text-success">Заказ успешно оплачен!</h2>
                <p class="text-muted">Спасибо за покупку. Мы уже начали собирать ваш заказ.</p>
                <a href="{{ route('orders.index') }}" class="btn btn-primary w-100 mt-3 py-2 fw-bold">
                    Перейти к списку заказов
                </a>
            </div>

            <!-- Состояние 3: Ошибка / Отмена оплаты (Изначально скрыто) -->
            <div id="status-failed" class="card p-5 shadow-sm border-0 d-none">
                <div class="mb-4 text-danger" style="font-size: 3.5rem;">❌</div>
                <h2 class="h4 mb-2 text-danger">Оплата не прошла</h2>
                <p class="text-muted small" id="error-reason">Транзакция была отклонена банком или время сессии истекло.</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-50 py-2">В корзину</a>
                    <a href="{{ route('yookassa.pay', $order->id) }}" class="btn btn-danger w-50 py-2 fw-bold">Попробовать снова</a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const orderId = "{{ $order->id }}";

        // Генерируем URL для опроса статуса (замените на ваш фактический путь API)
        const checkStatusUrl = `/api/payments/yookassa/status/${orderId}`;

        let attempts = 0;
        const maxAttempts = 20; // Опрашиваем в течение 60 секунд (20 раз по 3 секунды)

        const interval = setInterval(async () => {
            attempts++;

            try {
                const response = await fetch(checkStatusUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) return;

                const data = await response.json();

                // Если ВЕБХУК дошел, и платеж перешел в 'succeeded' или статус заказа стал 'paid'
                if (data.payment_status === 'succeeded' || data.order_status === 'paid') {
                    clearInterval(interval);
                    document.getElementById('status-processing').classList.add('d-none');
                    document.getElementById('status-success').classList.remove('d-none');
                    return;
                }

                // Если ЮKassa прислала отмену
                if (data.payment_status === 'canceled') {
                    clearInterval(interval);
                    document.getElementById('status-processing').classList.add('d-none');
                    document.getElementById('status-failed').classList.remove('d-none');
                    return;
                }

            } catch (error) {
                console.error('Ошибка проверки статуса платежа:', error);
            }

            // Если время вышло, а вебхук так и не долетел — отправляем пользователя в список заказов
            if (attempts >= maxAttempts) {
                clearInterval(interval);
                window.location.href = "{{ route('orders.index') }}";
            }
        }, 3000); // Опрос каждые 3 секунды
    });
</script>
@endsection
