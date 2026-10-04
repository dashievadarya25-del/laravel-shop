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
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();

            // Внешний ключ для связи с таблицей order_payments.
            // При удалении платежа удалятся и связанные чеки (onDelete('cascade')).
            $table->foreignId('order_payment_id')
                ->constrained('order_payments')
                ->onDelete('cascade');

            // ID чека во внешней системе (например, Атол, Эвотор, КлаудКассир)
            $table->string('external_receipt_id')->nullable()->index();

            // Тип чека (например: 'sell' — приход, 'sell_refund' — возврат прихода)
            $table->string('type');

            // Статус фискализации (например: 'pending', 'done', 'failed')
            $table->string('status');

            // Флаг: отправлять ли чек клиенту на email/phone (true/false)
            $table->boolean('send_to_customer')->default(true);

            // Логи запросов и ответов API онлайн-кассы в формате JSON
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();

            // Текст ошибки, если статус фискализации 'failed'
            $table->text('error_message')->nullable();

            $table->timestamps(); // Поля created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
