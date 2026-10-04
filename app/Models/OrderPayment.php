<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'provider',
        'status',
        'amount',
        'currency',
        'external_payment_id',
        'idempotence_key',
        'confirmation_url',
        'request_payload',
        'response_payload',
        'paid_at',
        'canceled_at',
        'error_message',
    ];

    /**
     * Автоматическое приведение типов для JSON-логов и дат.
     */
    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'paid_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    /**
     * Связь с заказом (каждый платеж принадлежит конкретному заказу)
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Связь с чеком (у одной попытки платежа может быть один фискальный чек)
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(PaymentReceipt::class, 'order_payment_id');
    }
}
