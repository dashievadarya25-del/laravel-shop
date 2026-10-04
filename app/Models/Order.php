<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELED = 'canceled';
    public const PAYMENT_METHOD_CASH = 'cash';
    public const PAYMENT_METHOD_YOOKASSA = 'yookassa';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Ожидает оплаты',
        self::STATUS_PAID => 'Оплачен',
        self::STATUS_SHIPPED => 'Отправлен',
        self::STATUS_COMPLETED => 'Завершен',
        self::STATUS_CANCELED => 'Отменен',

    ];

    public const PAYMENT_METHOD_LABELS = [
        self::PAYMENT_METHOD_CASH => 'Наличными при получении',
        self::PAYMENT_METHOD_YOOKASSA => 'Онлайн через ЮKassa',
    ];

    protected $fillable = [
        'user_id',
        'address_id',
        'total',
        'status',
        'payment_method',
        'shipping_address'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHOD_LABELS[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Связь с техническими платежами.
     * У одного заказа может быть несколько попыток оплаты (сессий).
     */
    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class, 'order_id')
            ->orderByDesc('created_at');
    }

}
