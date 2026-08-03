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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            // Связь с таблицей users. При удалении юзера — удалятся и его адреса
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Поля для самого адреса (адаптируйте под свои нужды)
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('house')->nullable();

            // Тот самый флаг дефолтного адреса. По умолчанию ставим false (0)
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
