<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Создаём ENUM тип в PostgreSQL
        DB::statement("CREATE TYPE cart_status AS ENUM ('active', 'ordered', 'abandoned')");

        Schema::create('carts', function (Blueprint $table) {
            $table->id()->comment('Первичный ключ корзины');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('Пользователь, владелец корзины');
            $table->enum('status', ['active', 'ordered', 'abandoned'])
                ->default('active')
                ->comment('Статус корзины: active, ordered, abandoned');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('carts');
        DB::statement('DROP TYPE IF EXISTS cart_status');
    }
};
