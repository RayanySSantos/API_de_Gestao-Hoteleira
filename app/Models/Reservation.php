<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'hotel_id',
        'room_id',
        'rate_id',
        'room_reservation_id',
        'customer_first_name',
        'customer_last_name',
        'reservation_date',
        'reservation_time',
        'check_in',
        'check_out',
        'currency_code',
        'meal_plan',
        'total_price',
    ];

    /**
     * Conversão automática de campos:
     * datas da reserva e formatação financeira do valor total.
     */
    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'check_in' => 'date',
            'check_out' => 'date',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * Relação Many-to-One.
     * A reserva pertence a um único hotel.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Relação Many-to-One.
     * A reserva pertence a um único quarto.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relação Many-to-One.
     * A reserva está associada a uma tarifa.
     */
    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }

    /**
     * Relação 1:N.
     * Uma reserva pode possuir vários hóspedes.
     */
    public function guests(): HasMany
    {
        return $this->hasMany(ReservationGuest::class);
    }

    /**
     * Relação 1:N.
     * Uma reserva pode possuir vários registros de preço.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ReservationPrice::class);
    }
}
