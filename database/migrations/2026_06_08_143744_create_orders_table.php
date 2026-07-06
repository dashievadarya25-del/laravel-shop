<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // ENUM тип для статуса заказа
        DB::statement("CREATE TYPE order_status AS ENUM ('pending', 'paid', 'shipped', 'completed', 'canceled')");

        Schema::create('orders', function (Blueprint $table) {
            $table->id()->comment('Первичный ключ заказа');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('Покупатель');
            $table->decimal('total', 12, 2)->comment('Общая сумма заказа');
            $table->enum('status', ['pending', 'paid', 'shipped', 'completed', 'canceled'])
                ->default('pending')
                ->comment('Статус заказа');
            $table->text('shipping_address')->nullable()->comment('Адрес доставки');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
        DB::statement('DROP TYPE IF EXISTS order_status');
    }
};
