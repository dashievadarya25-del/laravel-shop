<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // Например: 'stripe', 'paypal', 'yookassa'
            $table->string('status');   // Например: 'pending', 'paid', 'failed'

            // Для сумм рекомендуется использовать decimal (10 знаков всего, 2 после запятой)
            $table->decimal('amount', 10, 2);

            $table->string('currency', 3); // Исправлена опечатка. ISO-код валюты, например: 'USD', 'RUB'

            $table->string('external_payment_id')->nullable(); // ID во внешней системе платежей
            $table->string('idempotence_key')->unique();       // Ключ идемпотентности (обычно уникальный)
            $table->text('confirmation_url')->nullable();      // URL для подтверждения платежа

            // Данные запросов/ответов лучше хранить в json, чтобы легко их читать в Laravel
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();

            // Временные метки для статусов
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('canceled_at')->nullable();

            $table->text('error_message')->nullable(); // Сообщение об ошибке, если платеж не прошел
            $table->timestamps(); // Создает поля created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
