<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'sku'
    ];

    /**
     * Кастинг атрибутов для безопасной работы бизнес-логики корзины
     */
    protected $casts = [
        'stock' => 'integer',       // Гарантирует int для проверки остатков в SessionCartService
        'price' => 'decimal:2',     // Гарантирует string('0.00') для точных вычислений bcmath
    ];

    /**
     * Проверить, есть ли товар в наличии
     */
    public function hasStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Проверить, доступно ли запрашиваемое количество
     */
    public function isStockAvailable(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Связь с категорией товара
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
