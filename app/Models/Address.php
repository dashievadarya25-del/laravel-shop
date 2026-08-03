<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'city',
        'street',
        'house',
        'is_default'
    ];

    /**
     * Автоматическое приведение типов.
     * Поле is_default будет автоматически преобразовываться в true/false (boolean).
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Аксессор для получения полной строки адреса.
     * Позволяет вызывать $address->full_address в любом месте кода.
     */
    public function getFullAddressAttribute(): string
    {
        return sprintf(
            'г. %s, ул. %s, д. %s',
            $this->city,
            $this->street,
            $this->house
        );
    }

    /**
     * Обратная связь: адрес принадлежит пользователю.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
