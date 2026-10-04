<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    protected $fillable = [
        'order_payment_id',
        'external_receipt_id',
        'type',
        'status',
        'send_to_customer',
        'request_payload',
        'response_payload',
        'error_message',
    ];

    /**
     * Автоматическое приведение типов для JSON-логов и флагов.
     */
    protected $casts = [
        'send_to_customer' => 'boolean',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    /**
     * Обратная связь с платежной сессией.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(OrderPayment::class, 'order_payment_id');
    }
}
