<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationGuest extends Model
{
    protected $fillable = [
        'reservation_id', //// ID da reserva à qual o hóspede está associado
        'type', //Tipo do hóspede.
        'count', //Quantidade daquele tipo.
    ];

    protected function casts(): array
    {
        return [
            'count' => 'integer',
        ];
    }

    /**
     * Relação Many-to-One.
     * O hóspede pertence a uma única reserva.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
