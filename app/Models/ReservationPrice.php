<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationPrice extends Model
{
    protected $fillable = [
        'reservation_id', //Reserva vinculada
        'rate_id', //Tarifa usada naquele dia
        'price_date', //Data da diária
        'amount', // Valor da diária
    ];

    protected function casts(): array
    {
        return [
            'price_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Relação Many-to-One.
     * O registro de preço pertence a uma única reserva.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Relação Many-to-One.
     * O registro de preço está associado a uma tarifa.
     */
    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }
}
