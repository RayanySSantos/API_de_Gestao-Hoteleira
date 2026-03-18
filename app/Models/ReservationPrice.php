<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationPrice extends Model
{
    protected $fillable = [
        'reservation_id',
        'rate_id',
        'price_date',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'price_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function rate(): BelongsTo
    {
        return $this->belongsTo(Rate::class);
    }
}
